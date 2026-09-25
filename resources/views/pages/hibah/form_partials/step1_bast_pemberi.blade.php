<!-- ========================================================================= -->
<!-- LANGKAH 1: DOKUMEN BAST HIBAH & PIHAK PEMBERI                             -->
<!-- ========================================================================= -->
<div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
    
    <div>
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-amber-400/10 text-amber-300 border border-amber-400/20 text-xs font-bold mb-2">
            <span>🎁 LANGKAH 1 DARI 3: LEGALITAS HIBAH</span>
        </div>
        <h2 class="text-lg font-bold text-white flex items-center space-x-2">
            <span class="p-2 rounded-xl bg-amber-400/10 text-amber-400 text-sm">📜</span>
            <span>Langkah 1: Dokumen BAST &amp; Informasi Pihak Pemberi Hibah</span>
        </h2>
        <p class="text-xs text-slate-400 mt-1">
            Masukkan legalitas penyerahan (BAST Hibah), periode pembukuan, dan pihak pemberi hibah (tanpa pagu belanja APBD &amp; tanpa rekanan tender).
        </p>
    </div>



    <!-- Bagian BAST & Pemberi Hibah -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-amber-500/30 space-y-5 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <span class="text-xs font-extrabold text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                <span>🎁 Informasi Pihak Pemberi &amp; Legalitas BAST</span>
                <span class="text-rose-400">*</span>
            </span>
            <span class="text-[10px] font-bold text-amber-300 bg-amber-400/10 px-2 py-0.5 rounded-lg border border-amber-400/20">
                Non-Belanja Modal APBD
            </span>
        </div>

        <!-- Pihak Pemberi Hibah (Combobox / Filter Riwayat & Bebas Ketik) -->
        <div class="relative space-y-1.5" @click.away="isInstansiDropdownOpen = false">
            <div class="flex items-center justify-between">
                <label class="block text-xs font-bold text-slate-200">
                    Instansi / Lembaga Pemberi Hibah <span class="text-rose-400">*</span>
                </label>
                <template x-if="masterInstansiList && masterInstansiList.length > 0">
                    <span class="text-[10px] text-amber-400 font-mono font-normal flex items-center gap-1 bg-amber-500/10 px-2 py-0.5 rounded-md border border-amber-500/20">
                        <span>⚡</span>
                        <span>Riwayat Tersimpan</span>
                    </span>
                </template>
            </div>

            <!-- Input Box dengan Ikon dan Clear Button -->
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
                
                <!-- Ikon Instansi -->
                <svg class="w-4 h-4 text-amber-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>

                <!-- Tombol Kosongkan Input -->
                <template x-if="formData.hibah_pemberi">
                    <button type="button" 
                        @click="formData.hibah_pemberi = ''; isInstansiDropdownOpen = true" 
                        title="Kosongkan nama instansi"
                        class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 rounded-md bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 flex items-center justify-center text-xs transition-colors">
                        ✕
                    </button>
                </template>
            </div>

            <!-- Floating Dropdown Saran / Filter Instansi (Muncul saat fokus/diketik) -->
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

                <!-- Notifikasi jika mengetik entitas baru yang belum ada di daftar -->
                <template x-if="formData.hibah_pemberi && filteredInstansiList.length === 0">
                    <div class="p-3 text-center text-xs text-slate-400 bg-slate-950/50">
                        <span class="text-amber-300 font-semibold" x-text="'➕ Gunakan Instansi Baru: &quot;' + formData.hibah_pemberi + '&quot;'"></span>
                        <p class="text-[10px] text-slate-500 mt-0.5">Instansi ini akan otomatis tersimpan ke riwayat database setelah formulir disimpan.</p>
                    </div>
                </template>
            </div>
            
            <!-- Rekomendasi Cepat Pemberi Hibah (Badge Shortcut) -->
            <div class="pt-1 flex flex-wrap items-center gap-1.5">
                <span class="text-[10px] text-slate-500 font-semibold mr-1">Rekomendasi Cepat:</span>
                <button type="button" @click="selectInstansi('Kementerian Kesehatan Republik Indonesia')"
                    class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] border border-slate-700 cursor-pointer transition-colors">
                    Kemenkes RI
                </button>
                <button type="button" @click="selectInstansi('Dinas Kesehatan Provinsi Jawa Timur')"
                    class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] border border-slate-700 cursor-pointer transition-colors">
                    Dinkes Prov. Jatim
                </button>
                <button type="button" @click="selectInstansi('Pemerintah Kabupaten Bondowoso')"
                    class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] border border-slate-700 cursor-pointer transition-colors">
                    Pemkab Bondowoso
                </button>
                <button type="button" @click="selectInstansi('Donatur Swasta / Yayasan CSR')"
                    class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] border border-slate-700 cursor-pointer transition-colors">
                    Donatur Swasta / CSR
                </button>
            </div>
        </div>

        <!-- Nomor & Tanggal BAST -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Nomor BAST Hibah <span class="text-rose-400">*</span>
                </label>
                <input type="text" x-model="formData.hibah_nomor_bast" required
                    placeholder="Contoh: 028/BAST-HB/KEMENKES/2026"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white font-mono focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Tanggal BAST Hibah <span class="text-rose-400">*</span>
                </label>
                <input type="text" x-datepicker x-model="formData.hibah_tanggal_bast" required
                    placeholder="dd/mm/yyyy"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
            </div>
        </div>

        <!-- Nilai Taksiran Buku Hibah -->
        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800">
            <label class="block text-xs font-bold text-slate-200 mb-1.5 flex items-center justify-between">
                <span>Nilai Taksiran / Buku Hibah (Rp) <span class="text-slate-400 font-normal text-[11px]">(Boleh Rp 0 jika hibah tanpa taksiran harga)</span></span>
                <span class="text-xs font-mono text-emerald-400 font-black" x-text="formatRupiah(formData.total_realisasi)"></span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-2.5 text-slate-500 text-xs font-bold font-mono">Rp</span>
                <input type="text" 
                    :value="formData.total_realisasi ? Number(formData.total_realisasi).toLocaleString('id-ID') : ''"
                    @input="
                        let raw = $event.target.value.replace(/\D/g, '');
                        formData.total_realisasi = raw ? parseInt(raw, 10) : 0;
                        formData.jumlah_realisasi = formData.total_realisasi;
                        $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                    "
                    placeholder="0"
                    class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 pl-10 text-xs text-emerald-400 font-bold font-mono focus:outline-none">
            </div>
            <p class="text-[10.5px] text-slate-400 mt-1.5 italic">
                💡 Nilai ini juga akan otomatis sinkron dari rincian harga barang yang Anda input pada Langkah 3.
            </p>
        </div>
    </div>

</div>
