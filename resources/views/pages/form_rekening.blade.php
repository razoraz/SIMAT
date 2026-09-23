<x-layout title="Form Input Belanja Barang (Perbekalan) - SIMAT-RK">
    @section('page-title', 'Pencatatan Belanja Barang')
    @section('breadcrumb', 'Master Utama / Data ASTAP / Tambah Belanja Barang')

    <div x-data="formRekening()" x-cloak class="max-w-5xl mx-auto space-y-6 py-2">

        <!-- Top Header & Back -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('astap.pilih_jenis') }}"
                    class="p-2.5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-indigo-500/50 text-slate-400 hover:text-indigo-400 transition-all shadow-lg shadow-black/20 group">
                    <svg class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-2xl font-black text-white tracking-tight">
                            Pencatatan Belanja Barang (Perbekalan)
                        </h1>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-500/15 text-indigo-300 border border-indigo-500/30">
                            📦 PUSAT PERBEKALAN / BARANG &amp; JASA
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Pendaftaran barang perbekalan / operasional dari pusat perbekalan (rekening 5.1.02) untuk pengawasan fisik inventaris ruangan (KIR).
                    </p>
                </div>
            </div>
        </div>

        <!-- Stepper Navigation Bar (3 Langkah Bersih) -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-xl">
            <div class="grid grid-cols-3 gap-3 sm:gap-4">
                
                <!-- Step 1 Tab -->
                <button type="button" @click="goToStep(1)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 transition-all">
                    <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                        <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                             :class="step === 1 ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/30' : (step > 1 ? 'bg-indigo-500/20 text-indigo-400 border border-indigo-500/40' : 'bg-slate-950 text-slate-500 border border-slate-800')">
                            <span x-show="step <= 1">1</span>
                            <span x-show="step > 1">✓</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="step === 1 ? 'text-indigo-400' : 'text-slate-500'">Langkah 1</span>
                            <span class="text-[11px] sm:text-xs font-bold text-white block truncate">Dokumen Faktur &amp; Toko</span>
                        </div>
                    </div>
                    <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="step >= 1 ? 'bg-indigo-500' : 'bg-slate-950'"></div>
                </button>

                <!-- Step 2 Tab -->
                <button type="button" @click="goToStep(2)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 transition-all">
                    <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                        <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                             :class="step === 2 ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/30' : (step > 2 ? 'bg-indigo-500/20 text-indigo-400 border border-indigo-500/40' : 'bg-slate-950 text-slate-500 border border-slate-800')">
                            <span x-show="step <= 2">2</span>
                            <span x-show="step > 2">✓</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="step === 2 ? 'text-indigo-400' : 'text-slate-500'">Langkah 2</span>
                            <span class="text-[11px] sm:text-xs font-bold text-white block truncate">Klasifikasi 108</span>
                        </div>
                    </div>
                    <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="step >= 2 ? 'bg-indigo-500' : 'bg-slate-950'"></div>
                </button>

                <!-- Step 3 Tab (Rincian Teknis KIB & Penempatan Ruangan) -->
                <button type="button" @click="goToStep(3)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 transition-all">
                    <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                        <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                             :class="step === 3 ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/30' : 'bg-slate-950 text-slate-500 border border-slate-800'">
                            <span>3</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="step === 3 ? 'text-indigo-400' : 'text-slate-500'">Langkah 3</span>
                            <span class="text-[11px] sm:text-xs font-bold text-white block truncate" x-text="(hasSelectedKib ? kibLabel : 'Spesifikasi') + ' & Penempatan'"></span>
                        </div>
                    </div>
                    <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="step >= 3 ? 'bg-indigo-500' : 'bg-slate-950'"></div>
                </button>

            </div>
        </div>

        <!-- MAIN FORM CONTAINER -->
        <form @submit.prevent="submitForm" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">

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
                        Masukkan data faktur, nota pembelian toko, atau kuitansi pembayaran operasional perbekalan.
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

                    <!-- Toko / Supplier Rekanan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-200 mb-1.5">
                            Nama Toko / Supplier / Rekanan <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" x-model="formData.rekening_penyedia" required
                            placeholder="Contoh: Toko Barokah Medika, UD. Mandiri Elektrik, Apotek Sehat..."
                            class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none font-bold">
                        
                        <!-- Rekomendasi Cepat dari DB Penyedia jika ada -->
                        @if(!empty($dbPenyedias) && count($dbPenyedias) > 0)
                        <div class="mt-2 flex flex-wrap items-center gap-1.5">
                            <span class="text-[10px] text-slate-500 font-semibold mr-1">Toko Tersimpan:</span>
                            @foreach(array_slice($dbPenyedias, 0, 5) as $penyediaName)
                                <button type="button" @click="formData.rekening_penyedia = '{{ addslashes($penyediaName) }}'"
                                    class="px-2.5 py-0.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] border border-slate-700 cursor-pointer">
                                    {{ Str::limit($penyediaName, 25) }}
                                </button>
                            @endforeach
                        </div>
                        @endif
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
                            <input type="date" x-model="formData.rekening_tanggal_faktur" required
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

            <!-- ========================================================================= -->
            <!-- LANGKAH 2: KLASIFIKASI KODE BARANG 108                                    -->
            <!-- ========================================================================= -->
            <div x-show="step === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="border-b border-slate-800 pb-4">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-cyan-500/10 text-cyan-300 border border-cyan-500/20 text-xs font-bold mb-2">
                        <span>Langkah 2 dari 3</span>
                    </div>
                    <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                        <span>🔍 Klasifikasi Kode Barang (Permendagri 108)</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Tentukan klasifikasi barang Permendagri 108 agar NIBAR, QR code, dan buku inventaris terbuat dengan benar.
                    </p>
                </div>

                <!-- Bagian Klasifikasi Kode 108 -->
                <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-4">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-extrabold text-cyan-400 uppercase tracking-wider flex items-center gap-1.5">
                            <span>🔍 KLASIFIKASI KODE BARANG (PERMENDAGRI 108)</span>
                            <span class="text-rose-400">*</span>
                        </label>
                        <span x-show="selectedSubSub" class="text-[11px] font-bold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-lg border border-emerald-500/20">
                            ✓ Terpilih: <span x-text="selectedSubSub?.kode"></span>
                        </span>
                    </div>

                    <!-- Search Box Filter 108 -->
                    <div class="relative">
                        <input type="text"
                            x-model="search108"
                            @input="performSearch108()"
                            placeholder="Ketik nama atau kode barang (contoh: Tensimeter, Bed Pasien, Kursi Roda, USG, Laptop, AC, Lemari)..."
                            class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 pl-10 text-xs text-white placeholder-slate-500 focus:outline-none transition-all">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <button type="button" x-show="search108" @click="search108 = ''; searchResults108 = []"
                            class="absolute right-3 top-2.5 text-xs text-slate-400 hover:text-white">✕</button>
                    </div>

                    <!-- Hasil Pencarian 108 -->
                    <div x-show="searchResults108.length > 0" class="max-h-48 overflow-y-auto space-y-1 p-2 bg-slate-900 rounded-xl border border-cyan-500/30 custom-scrollbar">
                        <template x-for="item in searchResults108" :key="item.id">
                            <button type="button" @click="selectFromSearch(item)"
                                class="w-full text-left p-2 rounded-lg hover:bg-cyan-500/20 text-xs flex items-center justify-between group transition-colors">
                                <div class="truncate mr-2">
                                    <span class="font-mono text-cyan-300 font-bold" x-text="item.kode"></span>
                                    <span class="text-white ml-2" x-text="item.nama"></span>
                                </div>
                                <span class="text-[10px] text-slate-400 bg-slate-800 px-2 py-0.5 rounded group-hover:bg-cyan-500 group-hover:text-slate-950 font-bold shrink-0">Pilih</span>
                            </button>
                        </template>
                    </div>

                    <!-- Cascading Dropdowns -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-300 mb-1">1. Kelompok KIB Utama</label>
                            <select x-model="selectedJenisIdx" @change="onJenisChange()"
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                                <option value="">-- Pilih Kelompok KIB --</option>
                                <template x-for="(j, idx) in master108" :key="j.id">
                                    <option :value="idx" x-text="j.kode + ' - ' + j.nama"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-300 mb-1">2. Objek Barang</label>
                            <select x-model="selectedSubIdx" @change="onSubChange()" :disabled="!currentSubList.length"
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400 disabled:opacity-40">
                                <option value="">-- Pilih Objek --</option>
                                <template x-for="(s, idx) in currentSubList" :key="s.id">
                                    <option :value="idx" x-text="s.kode + ' - ' + s.nama"></option>
                                </template>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-bold text-slate-300 mb-1">3. Sub-Sub Rincian Objek (Detail Barang 108)</label>
                            <select @change="onSubSubChange($event)" :disabled="!currentSubSubList.length"
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400 disabled:opacity-40 font-mono">
                                <option value="">-- Pilih Sub-Sub Rincian Objek --</option>
                                <template x-for="ss in currentSubSubList" :key="ss.id">
                                    <option :value="ss.id" :selected="selectedSubSub && selectedSubSub.id === ss.id" x-text="ss.kode + ' - ' + ss.nama"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Input Nama Barang, Volume, Satuan -->
                <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-200 mb-1.5">
                            Nama Lengkap Barang <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" x-model="formData.nama_barang" required
                            placeholder="Contoh: Tensimeter Digital Omron HEM-7120 / Bed Pasien 3 Crank Elektrik"
                            class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none font-semibold">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                                Jumlah Volume <span class="text-rose-400">*</span>
                            </label>
                            <input type="number" x-model.number="formData.jumlah_volume" min="1" required
                                class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white font-mono focus:outline-none font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                                Satuan <span class="text-rose-400">*</span>
                            </label>
                            <input type="text" x-model="formData.satuan" required placeholder="Unit, Set, Buah, Rim..."
                                class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                                Harga Satuan (Otomatis)
                            </label>
                            <input type="text" readonly :value="formatRupiah(hargaSatuanHitung)"
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-emerald-400 font-mono font-bold focus:outline-none">
                        </div>
                    </div>
                </div>

            </div>

            <!-- ========================================================================= -->
            <!-- LANGKAH 3: RINCIAN SPESIFIKASI KIB & PENEMPATAN RUANGAN                   -->
            <!-- ========================================================================= -->
            <div x-show="step === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="border-b border-slate-800 pb-4">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full text-xs font-bold mb-2"
                         :class="isTanah ? 'bg-emerald-500/10 text-emerald-300 border border-emerald-500/20' : 'bg-indigo-500/10 text-indigo-300 border border-indigo-500/20'">
                        <span>Langkah 3 dari 3</span>
                    </div>
                    <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                        <span x-text="kibBadgeIcon"></span>
                        <span x-text="hasSelectedKib ? ('Spesifikasi Teknis (' + kibLabel + ') & Penempatan Ruangan') : 'Spesifikasi Teknis & Penempatan Ruangan'"></span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        <span x-show="!hasSelectedKib">Tentukan Klasifikasi KIB pada Langkah 2 terlebih dahulu agar formulir spesifikasi teknis barang dapat dimuat.</span>
                        <span x-show="hasSelectedKib && isTanah">Lengkapi data sertifikat, luas, dan batas bidang tanah (multi-bidang didukung).</span>
                        <span x-show="hasSelectedKib && !isTanah">Lengkapi spesifikasi teknis barang (Merk, Tipe, No Seri), lalu tentukan unit penempatan dan penanggung jawab KIR.</span>
                    </p>
                </div>

                <!-- Keadaan jika KIB belum dipilih di Langkah 2 -->
                <template x-if="!hasSelectedKib">
                    <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 text-center space-y-3">
                        <div class="w-12 h-12 rounded-full bg-slate-800 flex items-center justify-center mx-auto text-xl text-slate-400">🔍</div>
                        <h4 class="text-sm font-bold text-white">Kelompok KIB Belum Dipilih</h4>
                        <p class="text-xs text-slate-400 max-w-md mx-auto">Silakan kembali ke <strong>Langkah 2</strong> dan tentukan Kelompok KIB atau cari nama barang 108 terlebih dahulu agar spesifikasi teknis yang sesuai dapat dimuat.</p>
                        <button type="button" @click="step = 2" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-cyan-400 text-xs font-bold rounded-xl border border-slate-700 transition">
                            ← Kembali ke Langkah 2
                        </button>
                    </div>
                </template>

                <!-- --------------------------------------------------------------------- -->
                <!-- KONDISI A: FORM SPESIFIKASI TANAH (KIB A)                             -->
                <!-- --------------------------------------------------------------------- -->
                <template x-if="isTanah">
                    <div class="space-y-5">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl bg-emerald-950/30 border border-emerald-500/40 shadow-md">
                            <div class="space-y-0.5">
                                <div class="flex items-center space-x-2">
                                    <span class="p-1.5 rounded-lg bg-emerald-500/20 text-emerald-400 text-sm">🌾</span>
                                    <h3 class="text-xs sm:text-sm font-extrabold text-white tracking-wide uppercase">
                                        RINCIAN BIDANG TANAH (<span class="text-emerald-400" x-text="formData.tanah_items.length"></span> Bidang Terdaftar)
                                    </h3>
                                </div>
                                <p class="text-[11px] text-slate-400">
                                    Total Luas: <strong class="text-cyan-300" x-text="totalLuasTanah.toLocaleString('id-ID') + ' m²'"></strong> • Setiap bidang tanah memiliki sertifikat dan luas masing-masing.
                                </p>
                            </div>
                            <button type="button" @click="addTanahItem()" 
                                    class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs font-bold transition-all flex items-center justify-center space-x-1.5 shadow-lg shadow-emerald-500/20 shrink-0 cursor-pointer">
                                <span>➕ Tambah Bidang Tanah</span>
                            </button>
                        </div>

                        <!-- Repeater Kartu Tanah -->
                        <div class="space-y-5">
                            <template x-for="(item, idx) in formData.tanah_items" :key="idx">
                                <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-emerald-500/30 hover:border-emerald-500/60 transition-all space-y-4 shadow-xl relative group">
                                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="px-3 py-1 rounded-xl bg-emerald-500/20 text-emerald-300 font-mono font-extrabold text-xs border border-emerald-500/40 flex items-center space-x-1.5">
                                                <span>🌾 Bidang Tanah #<span x-text="idx + 1"></span></span>
                                            </span>
                                            <span class="text-[11px] text-slate-400 font-mono">
                                                • Luas: <strong class="text-cyan-300" x-text="(item.tanah_luas_m2 || 0).toLocaleString('id-ID') + ' m²'"></strong>
                                            </span>
                                            <span class="text-[11px] text-slate-400 font-mono">
                                                • Hak: <strong class="text-amber-300" x-text="item.tanah_hak || 'Hak Pakai'"></strong>
                                            </span>
                                        </div>

                                        <button type="button" 
                                                x-show="formData.tanah_items.length > 1" 
                                                @click="removeTanahItem(idx)" 
                                                class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 cursor-pointer">
                                            <span>🗑️ Hapus Bidang Ini</span>
                                        </button>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                            <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">📜 Status Tanah &amp; Sertifikat:</span>
                                            <div>
                                                <label class="block text-slate-400 text-[11px] mb-1 font-semibold">Hak Tanah</label>
                                                <select x-model="item.tanah_hak" @change="syncTanahFields()"
                                                    class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-amber-500">
                                                    <option value="Hak Pakai">Hak Pakai</option>
                                                    <option value="Hak Pengelolaan">Hak Pengelolaan</option>
                                                    <option value="Hak Milik">Hak Milik</option>
                                                    <option value="Lainnya">Lainnya</option>
                                                </select>
                                            </div>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1">Sertifikat Nomor</label>
                                                    <input type="text" x-model="item.tanah_sertifikat_no" @input="syncTanahFields()" placeholder="HP-108/1984"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-amber-500">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1">Sertifikat Tanggal</label>
                                                    <input type="date" x-model="item.tanah_sertifikat_tgl" @change="syncTanahFields()"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white focus:border-amber-500">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                            <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider">📐 Kondisi, Penggunaan &amp; Volume:</span>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1">Kondisi (B/KB/RB)</label>
                                                    <select x-model="item.tanah_kondisi" @change="syncTanahFields()"
                                                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold focus:border-cyan-500">
                                                        <option value="Baik">Baik (B)</option>
                                                        <option value="Kurang Baik">Kurang Baik (KB)</option>
                                                        <option value="Rusak Berat">Rusak Berat (RB)</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1">Jumlah Bidang</label>
                                                    <input type="number" min="1" x-model.number="item.tanah_jumlah_bidang" @input="syncTanahFields()"
                                                           placeholder="1"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono font-bold focus:border-cyan-500">
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1">Luas Tanah (m²)</label>
                                                    <input type="number" step="any" min="0" x-model.number="item.tanah_luas_m2" @input="syncTanahFields()"
                                                           placeholder="Contoh: 1250"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-cyan-300 font-mono font-bold focus:border-cyan-500">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1">Penggunaan Tanah</label>
                                                    <input type="text" x-model="item.tanah_penggunaan" @input="syncTanahFields()"
                                                           placeholder="Pelayanan Pasien"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-cyan-500">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- --------------------------------------------------------------------- -->
                <!-- KONDISI B: FORM PERALATAN & MESIN (KIB B)                             -->
                <!-- --------------------------------------------------------------------- -->
                <template x-if="isMesin">
                    <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Merk / Brand</label>
                                <input type="text" x-model="formData.merk" placeholder="Contoh: Omron / GE / Samsung / Sharp"
                                    class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Tipe / Model</label>
                                <input type="text" x-model="formData.type" placeholder="Contoh: HEM-7120 / Standar Medis"
                                    class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Nomor Pabrik / Serial Number (SN)</label>
                                <input type="text" x-model="formData.no_pabrik" placeholder="Nomor seri pabrikan barang"
                                    class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Ukuran / Dimensi / Kapasitas</label>
                                <input type="text" x-model="formData.ukuran" placeholder="Contoh: 120 x 80 x 75 cm / 500 Watt"
                                    class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Bahan / Material</label>
                                <input type="text" x-model="formData.bahan" placeholder="Contoh: Stainless Steel / Plastik ABS / Besi"
                                    class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Kondisi Fisik Saat Pembelian</label>
                                <select x-model="formData.kondisi"
                                    class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                    <option value="Baik">Baik (Baru / Berfungsi Optimal)</option>
                                    <option value="Kurang Baik">Kurang Baik</option>
                                    <option value="Rusak Ringan">Rusak Ringan</option>
                                    <option value="Rusak Berat">Rusak Berat</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- --------------------------------------------------------------------- -->
                <!-- KONDISI C: FORM GEDUNG, JARINGAN, ATB, LAINNYA                        -->
                <!-- --------------------------------------------------------------------- -->
                <template x-if="isGedung">
                    <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Luas Lantai (m²)</label>
                                <input type="number" min="0" step="any" x-model="formData.gedung_luas_m2" placeholder="Contoh: 450"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Kondisi Bangunan</label>
                                <select x-model="formData.kondisi"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                    <option value="Baik">Baik</option>
                                    <option value="Kurang Baik">Kurang Baik</option>
                                    <option value="Rusak Berat">Rusak Berat</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Bagian Penempatan Ruangan & PPK -->
                <div class="p-5 rounded-2xl bg-slate-950/80 border border-indigo-500/30 space-y-4 shadow-xl">
                    <span class="text-xs font-extrabold text-indigo-400 uppercase tracking-wider block">
                        🏢 Unit Penempatan Ruangan (KIR) &amp; Pejabat Pengesah RSUD:
                    </span>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                                Pejabat Pembuat Komitmen (PPK) / Pengurus Barang
                            </label>
                            <select x-model="formData.ppk_nama" @change="onPpkSelect()"
                                class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                <template x-for="p in pejabatsList" :key="p.nama">
                                    <option :value="p.nama" x-text="p.nama + (p.nip ? ' (' + p.nip + ')' : '')"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                                Unit / Ruangan Penempatan Aset (KIR) <span class="text-rose-400">*</span>
                            </label>
                            <select x-model="formData.unit_id" required
                                class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                <option value="">-- Pilih Unit / Ruangan --</option>
                                @foreach($dbUnits ?? [] as $u)
                                    <option value="{{ $u->id }}">{{ $u->nama }} ({{ $u->kode_unit ?? 'Unit' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                                Alamat / Gedung Penempatan Fisik
                            </label>
                            <input type="text" x-model="formData.alamat_barang"
                                placeholder="RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1"
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- Ringkasan Konfirmasi Card -->
                <div class="p-5 rounded-2xl bg-slate-950 border border-indigo-500/30 space-y-3 shadow-xl">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                        <span class="text-xs font-extrabold text-indigo-400 uppercase tracking-wider flex items-center gap-2">
                            <span>📋 Ringkasan Pendaftaran Belanja Rekening</span>
                        </span>
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-indigo-500/10 text-indigo-300 border border-indigo-500/20" x-text="kibLabel"></span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-slate-300">
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase font-bold">Nama Barang:</span>
                            <span class="font-bold text-white text-sm" x-text="formData.nama_barang || '-'"></span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase font-bold">Klasifikasi 108:</span>
                            <span class="font-bold text-cyan-300" x-text="selectedSubSub ? (selectedSubSub.kode + ' - ' + selectedSubSub.nama) : '-'"></span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase font-bold">Toko / Supplier:</span>
                            <span class="font-bold text-indigo-300" x-text="formData.rekening_penyedia || '-'"></span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase font-bold">Dokumen Faktur/Nota:</span>
                            <span class="font-mono text-slate-200 font-semibold" x-text="(formData.rekening_nomor_faktur || '-') + ' (' + formatTanggalIndo(formData.rekening_tanggal_faktur) + ')'"></span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase font-bold">Volume / Satuan:</span>
                            <span class="font-bold text-white font-mono" x-text="formData.jumlah_volume + ' ' + formData.satuan"></span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase font-bold">Total Pembelian:</span>
                            <span class="font-bold text-emerald-400 font-mono" x-text="formatRupiah(formData.total_realisasi)"></span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ========================================================================= -->
            <!-- BOTTOM NAVIGATION BUTTONS                                                 -->
            <!-- ========================================================================= -->
            <div class="pt-6 border-t border-slate-800 flex items-center justify-between">
                <div>
                    <button type="button" x-show="step > 1" @click="prevStep()"
                        class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs transition-all flex items-center space-x-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span>Kembali</span>
                    </button>
                </div>

                <div class="flex items-center space-x-3">
                    <a href="{{ route('astap.pilih_jenis') }}"
                        class="px-4 py-2.5 rounded-xl text-slate-400 hover:text-rose-400 font-bold text-xs transition-colors">
                        Batal
                    </a>

                    <!-- Next Step Button -->
                    <button type="button" x-show="step < 3" @click="nextStep()"
                        class="px-6 py-2.5 rounded-xl bg-indigo-500 hover:bg-indigo-400 text-white font-extrabold text-xs shadow-lg shadow-indigo-500/20 transition-all flex items-center space-x-2 cursor-pointer">
                        <span>Lanjutkan</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <!-- Final Submit Button (Langkah 3) -->
                    <button type="submit" x-show="step === 3" :disabled="isSubmitting"
                        class="px-8 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 disabled:opacity-50 text-slate-950 font-black text-xs shadow-xl shadow-emerald-500/25 transition-all flex items-center space-x-2 cursor-pointer">
                        <span x-show="!isSubmitting">💾 Simpan Belanja Barang</span>
                        <span x-show="isSubmitting">Menyimpan...</span>
                    </button>
                </div>
            </div>

        </form>

    </div>

    <!-- Alpine.js Script Implementation -->
    <script>
        function formRekening() {
            return {
                step: 1,
                isSubmitting: false,
                master108: @json($dbMaster108 ?? []),
                pejabatsList: @json($dbPejabats ?? []),

                // Form State
                formData: {
                    sumber_dana: 'belanja_barang',
                    tahun_perolehan: new Date().getFullYear(),
                    triwulan: 'TW I',
                    rekening_penyedia: '',
                    rekening_nomor_faktur: '',
                    rekening_tanggal_faktur: new Date().toISOString().split('T')[0],
                    total_realisasi: 0,
                    rekening_keterangan: '',
                    nama_barang: '',
                    jenis_astap_id: '',
                    jumlah_volume: 1,
                    satuan: 'Unit',
                    kondisi: 'Baik',
                    unit_id: '',
                    alamat_barang: 'RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1',
                    ppk_nama: '',
                    ppk_nip: '',
                    
                    // Spesifikasi Tanah (KIB A)
                    tanah_items: [
                        {
                            tanah_hak: 'Hak Pakai',
                            tanah_sertifikat_tgl: '',
                            tanah_sertifikat_no: '',
                            tanah_kondisi: 'Baik',
                            tanah_penggunaan: 'Bangunan Fasilitas Kesehatan & Pelayanan Rumah Sakit',
                            tanah_jumlah_bidang: 1,
                            tanah_luas_m2: '',
                            tanah_alamat: '',
                            tanah_nilai_fisik: 0
                        }
                    ],
                    sertifikat_nomor: '',

                    // Spesifikasi Peralatan & Mesin (KIB B)
                    merk: '',
                    type: '',
                    no_pabrik: '',
                    ukuran: '',
                    bahan: '',
                    no_rangka: '',
                    no_mesin: '',
                    no_polisi: '',

                    // Spesifikasi Gedung & Bangunan (KIB C)
                    gedung_luas_m2: '',
                    gedung_bertingkat: 'Tidak',
                    gedung_beton: 'Beton',
                    gedung_status_tanah: 'Tanah Pemda'
                },

                // 108 Selection State
                selectedJenisIdx: '',
                selectedSubIdx: '',
                selectedSubSub: null,
                currentSubList: [],
                currentSubSubList: [],
                search108: '',
                searchResults108: [],
                allFlattened108: [],

                init() {
                    const m = new Date().getMonth() + 1;
                    if (m >= 1 && m <= 3) this.formData.triwulan = 'TW I';
                    else if (m >= 4 && m <= 6) this.formData.triwulan = 'TW II';
                    else if (m >= 7 && m <= 9) this.formData.triwulan = 'TW III';
                    else this.formData.triwulan = 'TW IV';

                    this.flatten108();

                    if (this.pejabatsList && this.pejabatsList.length > 0) {
                        this.formData.ppk_nama = this.pejabatsList[0].nama;
                        this.formData.ppk_nip = this.pejabatsList[0].nip || '';
                    }
                },

                get hargaSatuanHitung() {
                    const v = parseInt(this.formData.jumlah_volume) || 1;
                    const r = parseFloat(this.formData.total_realisasi) || 0;
                    return Math.round(r / Math.max(1, v));
                },

                get selectedKibKode() {
                    if (this.selectedSubSub && this.selectedSubSub.jenisKode) {
                        return this.selectedSubSub.jenisKode;
                    }
                    if (this.selectedJenisIdx !== '' && this.master108[this.selectedJenisIdx]) {
                        return this.master108[this.selectedJenisIdx].kode;
                    }
                    return '';
                },

                get selectedKibNama() {
                    if (this.selectedSubSub && this.selectedSubSub.jenisNama) {
                        return this.selectedSubSub.jenisNama;
                    }
                    if (this.selectedJenisIdx !== '' && this.master108[this.selectedJenisIdx]) {
                        return this.master108[this.selectedJenisIdx].nama;
                    }
                    return '';
                },

                get isTanah() {
                    return Boolean(this.selectedKibKode.startsWith('1.3.1') || this.selectedKibNama.includes('TANAH'));
                },

                get isMesin() {
                    return Boolean(this.selectedKibKode.startsWith('1.3.2') || this.selectedKibNama.includes('PERALATAN') || this.selectedKibNama.includes('MESIN'));
                },

                get isGedung() {
                    return Boolean(this.selectedKibKode.startsWith('1.3.3') || this.selectedKibNama.includes('GEDUNG') || this.selectedKibNama.includes('BANGUNAN'));
                },

                get hasSelectedKib() {
                    return Boolean(this.selectedKibKode || (this.selectedJenisIdx !== '' && this.selectedJenisIdx !== null && this.selectedJenisIdx !== undefined));
                },

                get kibLabel() {
                    if (!this.hasSelectedKib) return 'Belum Dipilih';
                    if (this.isTanah) return 'Rincian Tanah';
                    if (this.isGedung) return 'Rincian Gedung';
                    if (this.isMesin) return 'Rincian Mesin & Alat';
                    return 'Rincian Aset Tetap';
                },

                get kibBadgeIcon() {
                    if (!this.hasSelectedKib) return '🔍';
                    if (this.isTanah) return '🌾';
                    if (this.isGedung) return '🏢';
                    if (this.isMesin) return '⚙️';
                    return '📦';
                },

                get totalLuasTanah() {
                    if (!this.formData.tanah_items || !this.formData.tanah_items.length) return 0;
                    return this.formData.tanah_items.reduce((acc, curr) => acc + (parseFloat(curr.tanah_luas_m2) || 0), 0);
                },

                addTanahItem() {
                    this.formData.tanah_items.push({
                        tanah_hak: 'Hak Pakai',
                        tanah_sertifikat_tgl: '',
                        tanah_sertifikat_no: '',
                        tanah_kondisi: 'Baik',
                        tanah_penggunaan: 'Bangunan Fasilitas Kesehatan & Pelayanan Rumah Sakit',
                        tanah_jumlah_bidang: 1,
                        tanah_luas_m2: '',
                        tanah_alamat: '',
                        tanah_nilai_fisik: 0
                    });
                    this.syncTanahFields();
                },

                removeTanahItem(idx) {
                    if (this.formData.tanah_items.length > 1) {
                        this.formData.tanah_items.splice(idx, 1);
                        this.syncTanahFields();
                    }
                },

                syncTanahFields() {
                    if (!this.formData.tanah_items.length) return;
                    const first = this.formData.tanah_items[0];
                    this.formData.sertifikat_nomor = first.tanah_sertifikat_no || '';
                    this.formData.jumlah_volume = this.formData.tanah_items.length;
                    this.formData.satuan = 'Bidang';
                },

                flatten108() {
                    const list = [];
                    if (!this.master108 || !Array.isArray(this.master108)) return;
                    this.master108.forEach(j => {
                        if (j.sub_kategori) {
                            j.sub_kategori.forEach(s => {
                                if (s.sub_sub_kategori) {
                                    s.sub_sub_kategori.forEach(ss => {
                                        list.push({
                                            id: ss.id,
                                            kode: ss.kode,
                                            nama: ss.nama,
                                            jenisId: j.id,
                                            jenisKode: j.kode,
                                            jenisNama: j.nama,
                                            subId: s.id,
                                            subKode: s.kode
                                        });
                                    });
                                }
                            });
                        }
                    });
                    this.allFlattened108 = list;
                },

                performSearch108() {
                    if (!this.search108 || this.search108.length < 2) {
                        this.searchResults108 = [];
                        return;
                    }
                    const q = this.search108.toLowerCase();
                    this.searchResults108 = this.allFlattened108.filter(it => 
                        it.kode.toLowerCase().includes(q) || it.nama.toLowerCase().includes(q)
                    ).slice(0, 15);
                },

                selectFromSearch(item) {
                    this.selectedSubSub = item;
                    this.formData.jenis_astap_id = item.id;
                    if (!this.formData.nama_barang) {
                        this.formData.nama_barang = item.nama;
                    }
                    const jIdx = this.master108.findIndex(j => j.id === item.jenisId);
                    if (jIdx !== -1) {
                        this.selectedJenisIdx = jIdx;
                        this.currentSubList = this.master108[jIdx].sub_kategori || [];
                        const sIdx = this.currentSubList.findIndex(s => s.id === item.subId);
                        if (sIdx !== -1) {
                            this.selectedSubIdx = sIdx;
                            this.currentSubSubList = this.currentSubList[sIdx].sub_sub_kategori || [];
                        }
                    }
                    this.search108 = '';
                    this.searchResults108 = [];
                    if (this.isTanah) {
                        this.formData.satuan = 'Bidang';
                    } else if (this.formData.satuan === 'Bidang') {
                        this.formData.satuan = 'Unit';
                    }
                },

                onJenisChange() {
                    if (this.selectedJenisIdx === '') {
                        this.currentSubList = [];
                        this.currentSubSubList = [];
                        this.selectedSubIdx = '';
                        this.selectedSubSub = null;
                        this.formData.jenis_astap_id = '';
                        this.formData.satuan = 'Unit';
                        return;
                    }
                    this.currentSubList = this.master108[this.selectedJenisIdx].sub_kategori || [];
                    this.currentSubSubList = [];
                    this.selectedSubIdx = '';
                    this.selectedSubSub = null;
                    this.formData.jenis_astap_id = '';
                    if (this.isTanah) {
                        this.formData.satuan = 'Bidang';
                    } else if (this.formData.satuan === 'Bidang') {
                        this.formData.satuan = 'Unit';
                    }
                },

                onSubChange() {
                    if (this.selectedSubIdx === '') {
                        this.currentSubSubList = [];
                        this.selectedSubSub = null;
                        this.formData.jenis_astap_id = '';
                        return;
                    }
                    this.currentSubSubList = this.currentSubList[this.selectedSubIdx].sub_sub_kategori || [];
                    this.selectedSubSub = null;
                    this.formData.jenis_astap_id = '';
                },

                onSubSubChange(e) {
                    const id = parseInt(e.target.value);
                    if (!id) {
                        this.selectedSubSub = null;
                        this.formData.jenis_astap_id = '';
                        return;
                    }
                    const found = this.currentSubSubList.find(x => x.id === id);
                    if (found) {
                        const j = this.master108[this.selectedJenisIdx];
                        this.selectedSubSub = {
                            id: found.id,
                            kode: found.kode,
                            nama: found.nama,
                            jenisId: j ? j.id : null,
                            jenisKode: j ? j.kode : '',
                            jenisNama: j ? j.nama : ''
                        };
                        this.formData.jenis_astap_id = found.id;
                        if (!this.formData.nama_barang) {
                            this.formData.nama_barang = found.nama;
                        }
                    }
                },

                onPpkSelect() {
                    const p = this.pejabatsList.find(x => x.nama === this.formData.ppk_nama);
                    if (p) this.formData.ppk_nip = p.nip || '';
                },

                formatRupiah(val) {
                    const n = parseFloat(val) || 0;
                    return 'Rp ' + n.toLocaleString('id-ID');
                },

                formatTanggalIndo(dateStr) {
                    if (!dateStr) return '-';
                    const parts = dateStr.split('-');
                    if (parts.length === 3) return parts[2] + '/' + parts[1] + '/' + parts[0];
                    return dateStr;
                },

                goToStep(s) {
                    if (s > this.step) {
                        if (!this.validateStep(this.step)) return;
                    }
                    this.step = s;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                nextStep() {
                    if (this.validateStep(this.step)) {
                        this.step++;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                },

                prevStep() {
                    if (this.step > 1) {
                        this.step--;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                },

                validateStep(s) {
                    if (s === 1) {
                        if (!this.formData.rekening_penyedia.trim()) {
                            alert('⚠️ Mohon isi Nama Toko / Supplier / Rekanan.');
                            return false;
                        }
                        if (!this.formData.rekening_nomor_faktur.trim()) {
                            alert('⚠️ Mohon isi Nomor Faktur / Nota Pembelian.');
                            return false;
                        }
                        if (!this.formData.rekening_tanggal_faktur) {
                            alert('⚠️ Mohon isi Tanggal Faktur / Pembelian.');
                            return false;
                        }
                        return true;
                    }
                    if (s === 2) {
                        if (!this.formData.jenis_astap_id) {
                            alert('⚠️ Mohon pilih Klasifikasi Kode Barang 108.');
                            return false;
                        }
                        if (!this.formData.nama_barang.trim()) {
                            alert('⚠️ Mohon isi Nama Lengkap Barang.');
                            return false;
                        }
                        return true;
                    }
                    if (s === 3) {
                        if (!this.formData.unit_id) {
                            alert('⚠️ Mohon pilih Unit / Ruangan Penempatan Aset (KIR).');
                            return false;
                        }
                        if (this.isTanah) {
                            this.syncTanahFields();
                        }
                        return true;
                    }
                    return true;
                },

                submitForm() {
                    if (!this.validateStep(1) || !this.validateStep(2) || !this.validateStep(3)) return;
                    
                    if (this.isTanah) {
                        this.syncTanahFields();
                    }

                    this.isSubmitting = true;
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                    fetch("{{ route('astap.store_rekening') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify(this.formData)
                    })
                    .then(res => res.json().then(data => ({ status: res.status, body: data })))
                    .then(result => {
                        this.isSubmitting = false;
                        if (result.status === 200 && result.body.success) {
                            alert('🎉 Berhasil! ' + (result.body.message || 'Data Belanja Barang berhasil disimpan.'));
                            window.location.href = "{{ route('astap.index') }}";
                        } else {
                            const errMsg = result.body.message || (result.body.errors ? Object.values(result.body.errors).flat().join('\n') : 'Gagal menyimpan data.');
                            alert('❌ Terjadi Kesalahan:\n' + errMsg);
                        }
                    })
                    .catch(err => {
                        this.isSubmitting = false;
                        console.error(err);
                        alert('❌ Gagal menghubungi server. Silakan coba kembali.');
                    });
                }
            };
        }
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.6);
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(99, 102, 241, 0.4);
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(99, 102, 241, 0.7);
        }
    </style>
</x-layout>
