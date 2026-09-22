<x-layout title="Form Input Aset Hibah - SIMAT-RK">
    @section('page-title', 'Form Input Aset Hibah')
    @section('breadcrumb', 'Master Utama / Data ASTAP / Tambah Hibah')

    <div x-data="formHibah()" x-cloak class="max-w-5xl mx-auto space-y-6 py-2">

        <!-- Top Header & Back -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('astap.pilih_jenis') }}"
                    class="p-2.5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-amber-500/50 text-slate-400 hover:text-amber-400 transition-all shadow-lg shadow-black/20 group">
                    <svg class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-2xl font-black text-white tracking-tight">
                            Pencatatan Aset Hibah
                        </h1>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-400/15 text-amber-300 border border-amber-400/30">
                            🎁 RMB HIBAH
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Daftarkan aset perolehan hibah / bantuan dari pihak ketiga tanpa melalui belanja modal APBD.
                    </p>
                </div>
            </div>

            <!-- Stepper Progress Pills -->
            <div class="flex items-center space-x-2 bg-slate-900/90 border border-slate-800 p-1.5 rounded-2xl">
                <button type="button" @click="goToStep(1)"
                    class="flex items-center space-x-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all"
                    :class="step === 1 ? 'bg-amber-400 text-slate-950 shadow-md shadow-amber-400/20' : (step > 1 ? 'text-amber-400 hover:bg-slate-800' : 'text-slate-500 hover:bg-slate-800')">
                    <span class="w-4 h-4 rounded-full flex items-center justify-center text-[10px]"
                        :class="step === 1 ? 'bg-slate-950 text-amber-400' : (step > 1 ? 'bg-amber-400 text-slate-950' : 'bg-slate-800 text-slate-400')">
                        <span x-show="step <= 1">1</span>
                        <span x-show="step > 1">✓</span>
                    </span>
                    <span>Identitas</span>
                </button>

                <div class="w-3 h-0.5 bg-slate-800"></div>

                <button type="button" @click="goToStep(2)"
                    class="flex items-center space-x-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all"
                    :class="step === 2 ? 'bg-amber-400 text-slate-950 shadow-md shadow-amber-400/20' : (step > 2 ? 'text-amber-400 hover:bg-slate-800' : 'text-slate-500 hover:bg-slate-800')">
                    <span class="w-4 h-4 rounded-full flex items-center justify-center text-[10px]"
                        :class="step === 2 ? 'bg-slate-950 text-amber-400' : (step > 2 ? 'bg-amber-400 text-slate-950' : 'bg-slate-800 text-slate-400')">
                        <span x-show="step <= 2">2</span>
                        <span x-show="step > 2">✓</span>
                    </span>
                    <span>BAST Hibah</span>
                </button>

                <div class="w-3 h-0.5 bg-slate-800"></div>

                <button type="button" @click="goToStep(3)"
                    class="flex items-center space-x-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all"
                    :class="step === 3 ? 'bg-amber-400 text-slate-950 shadow-md shadow-amber-400/20' : 'text-slate-500 hover:bg-slate-800'">
                    <span class="w-4 h-4 rounded-full flex items-center justify-center text-[10px]"
                        :class="step === 3 ? 'bg-slate-950 text-amber-400' : 'bg-slate-800 text-slate-400'">
                        3
                    </span>
                    <span>Konfirmasi</span>
                </button>
            </div>
        </div>

        <!-- MAIN FORM CONTAINER -->
        <form @submit.prevent="submitForm" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">

            <!-- ========================================================================= -->
            <!-- LANGKAH 1: IDENTITAS & KLASIFIKASI BARANG HIBAH                           -->
            <!-- ========================================================================= -->
            <div x-show="step === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="border-b border-slate-800 pb-4">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-amber-400/10 text-amber-300 border border-amber-400/20 text-xs font-bold mb-2">
                        <span>Langkah 1 dari 3</span>
                    </div>
                    <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                        <span>📋 Identitas & Klasifikasi Aset Hibah</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Tentukan klasifikasi kode barang Permendagri 108, nama barang, volume, satuan, dan estimasi nilai perolehan hibah.
                    </p>
                </div>

                <!-- Bagian 108 Master Selection -->
                <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-4">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-extrabold text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                            <span>🔍 Klasifikasi Kode Barang (Permendagri 108)</span>
                            <span class="text-rose-400">*</span>
                        </label>
                        <span x-show="selectedSubSub" class="text-[11px] font-bold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-lg border border-emerald-500/20">
                            ✓ Terpilih: <span x-text="selectedSubSub.kode"></span>
                        </span>
                    </div>

                    <!-- Search Box Filter 108 -->
                    <div class="relative">
                        <input type="text"
                            x-model="search108"
                            @input="performSearch108()"
                            placeholder="Ketik untuk mencari jenis aset (contoh: USG, Bed Pasien, Laptop, Mobil, Ambulance)..."
                            class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 pl-10 text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-amber-400 transition-all">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <button type="button" x-show="search108" @click="search108 = ''; searchResults108 = []"
                            class="absolute right-3 top-2.5 text-xs text-slate-400 hover:text-white">
                            ✕
                        </button>
                    </div>

                    <!-- Quick Search Results List -->
                    <div x-show="searchResults108.length > 0" class="max-h-52 overflow-y-auto space-y-1.5 p-2 bg-slate-900 rounded-xl border border-amber-400/30 custom-scrollbar">
                        <div class="text-[10px] uppercase font-extrabold text-slate-400 px-2 py-1">Hasil Pencarian Cepat:</div>
                        <template x-for="item in searchResults108" :key="item.id">
                            <div @click="select108Direct(item)"
                                class="p-2.5 rounded-lg bg-slate-950 hover:bg-amber-400/10 border border-slate-800 hover:border-amber-400/40 cursor-pointer transition-all flex items-center justify-between group">
                                <div class="min-w-0 pr-3">
                                    <div class="text-xs font-bold text-white group-hover:text-amber-300" x-text="item.nama"></div>
                                    <div class="text-[10px] text-slate-400" x-text="item.kode + ' • ' + item.parentNama"></div>
                                </div>
                                <span class="text-[11px] font-bold text-amber-400 group-hover:underline shrink-0">Pilih →</span>
                            </div>
                        </template>
                    </div>

                    <!-- Cascading Dropdowns Fallback / Manual Select -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-2">
                        <!-- 1. Kategori KIB (Jenis) -->
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-300 mb-1">1. Kelompok KIB</label>
                            <select x-model="selectedJenisIdx" @change="onJenisChange()"
                                class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                <option value="">-- Pilih Kelompok KIB --</option>
                                <template x-for="(j, idx) in master108" :key="j.kode">
                                    <option :value="idx" x-text="j.kode + ' - ' + j.nama"></option>
                                </template>
                            </select>
                        </div>

                        <!-- 2. Sub Rincian -->
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-300 mb-1">2. Sub Rincian</label>
                            <select x-model="selectedSubIdx" @change="onSubChange()" :disabled="!currentSubList.length"
                                class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none disabled:opacity-50">
                                <option value="">-- Pilih Sub Rincian --</option>
                                <template x-for="(s, idx) in currentSubList" :key="s.kode">
                                    <option :value="idx" x-text="s.kode + ' - ' + s.nama"></option>
                                </template>
                            </select>
                        </div>

                        <!-- 3. Sub-Sub Rincian (Barang Spesifik) -->
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-300 mb-1">3. Objek Barang Spesifik</label>
                            <select x-model="formData.jenis_astap_id" @change="onSubSubChange()" :disabled="!currentSubSubList.length"
                                class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none disabled:opacity-50">
                                <option value="">-- Pilih Objek 108 --</option>
                                <template x-for="ss in currentSubSubList" :key="ss.id">
                                    <option :value="ss.id" x-text="ss.kode + ' - ' + ss.nama"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Selected Detail Card -->
                    <div x-show="selectedSubSub" class="p-3 rounded-xl bg-amber-400/5 border border-amber-400/20 flex items-center justify-between">
                        <div class="text-xs">
                            <span class="text-slate-400">Klasifikasi Terpilih: </span>
                            <span class="font-bold text-amber-300" x-text="selectedSubSub?.kode + ' — ' + selectedSubSub?.nama"></span>
                        </div>
                        <button type="button" @click="clear108Selection()" class="text-[11px] font-bold text-rose-400 hover:text-rose-300">
                            ✕ Ganti
                        </button>
                    </div>
                </div>

                <!-- Input Detail Barang -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <!-- Nama Barang -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-200 mb-1.5 flex items-center justify-between">
                            <span>Nama Lengkap Barang / Merk / Tipe <span class="text-rose-400">*</span></span>
                            <span class="text-[10px] text-slate-400">Dapat disesuaikan dengan fisik BAST</span>
                        </label>
                        <input type="text"
                            x-model="formData.nama_barang"
                            placeholder="Contoh: USG Mindray DC-30 Color Doppler Portable Serial No: 88291..."
                            required
                            class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-amber-400 font-semibold">
                    </div>

                    <!-- Tahun Perolehan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-200 mb-1.5">
                            Tahun Perolehan Hibah <span class="text-rose-400">*</span>
                        </label>
                        <input type="number"
                            x-model.number="formData.tahun_perolehan"
                            min="1990" max="2100"
                            required
                            class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                    </div>

                    <!-- Triwulan Pembukuan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-200 mb-1.5">
                            Triwulan Pembukuan <span class="text-rose-400">*</span>
                        </label>
                        <select x-model="formData.triwulan" required
                            class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                            <option value="TW I">Triwulan I (Januari - Maret)</option>
                            <option value="TW II">Triwulan II (April - Juni)</option>
                            <option value="TW III">Triwulan III (Juli - September)</option>
                            <option value="TW IV">Triwulan IV (Oktober - Desember)</option>
                        </select>
                    </div>

                    <!-- Jumlah Volume -->
                    <div>
                        <label class="block text-xs font-bold text-slate-200 mb-1.5">
                            Jumlah Volume / Kuantitas <span class="text-rose-400">*</span>
                        </label>
                        <div class="relative">
                            <input type="number"
                                x-model.number="formData.jumlah_volume"
                                min="1"
                                required
                                class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                            <span class="absolute right-3.5 top-2.5 text-xs text-slate-400 font-bold">Unit/Item</span>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">Sistem otomatis men-generate NIBAR dan register sebanyak kuantitas ini.</p>
                    </div>

                    <!-- Satuan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-200 mb-1.5">
                            Satuan <span class="text-rose-400">*</span>
                        </label>
                        <div class="flex gap-2">
                            <select x-model="formData.satuan"
                                class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                                <option value="Unit">Unit</option>
                                <option value="Set">Set</option>
                                <option value="Buah">Buah</option>
                                <option value="Paket">Paket</option>
                                <option value="Lembar">Lembar</option>
                                <option value="Bidang">Bidang</option>
                                <option value="Dus">Dus</option>
                                <option value="Lusin">Lusin</option>
                            </select>
                            <input type="text"
                                x-model="formData.satuan"
                                placeholder="Lainnya..."
                                class="w-32 bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none">
                        </div>
                    </div>

                    <!-- Total Nilai Hibah -->
                    <div>
                        <label class="block text-xs font-bold text-slate-200 mb-1.5 flex items-center justify-between">
                            <span>Total Nilai Hibah (Rp) <span class="text-rose-400">*</span></span>
                            <span class="text-[11px] font-bold text-amber-300" x-text="formatRupiah(formData.total_realisasi)"></span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-2.5 text-xs font-bold text-amber-400">Rp</span>
                            <input type="number"
                                x-model.number="formData.total_realisasi"
                                min="0" step="any"
                                placeholder="0"
                                required
                                class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 pl-10 text-xs text-white font-bold focus:outline-none">
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">Sesuai nilai yang tercantum dalam BAST Hibah / Perjanjian Hibah.</p>
                    </div>

                    <!-- Harga Satuan (Otomatis) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-400 mb-1.5">
                            Nilai Satuan (Otomatis)
                        </label>
                        <div class="p-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs font-bold text-slate-300">
                            <span x-text="formatRupiah(computedHargaSatuan)"></span>
                            <span class="text-[10px] font-normal text-slate-400"> / <span x-text="formData.satuan"></span></span>
                        </div>
                    </div>

                    <!-- Penempatan / Unit -->
                    <div>
                        <label class="block text-xs font-bold text-slate-200 mb-1.5">
                            Unit / Ruangan Penempatan
                        </label>
                        <select x-model="formData.unit_id"
                            class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                            <option value="">-- Pilih Unit / Ruangan (Opsional) --</option>
                            @foreach($dbUnits as $u)
                                <option value="{{ $u->id }}">{{ $u->nama }} ({{ $u->kode_unit ?? 'Unit' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kondisi Barang Saat Diterima -->
                    <div>
                        <label class="block text-xs font-bold text-slate-200 mb-1.5">
                            Kondisi Fisik Barang Saat Diterima
                        </label>
                        <select x-model="formData.kondisi"
                            class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                            <option value="Baik">Baik (Baru / Berfungsi Optimal)</option>
                            <option value="Rusak Ringan">Rusak Ringan (Perlu Kalibrasi / Minor)</option>
                            <option value="Rusak Berat">Rusak Berat</option>
                        </select>
                    </div>

                    <!-- Alamat / Lokasi -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-200 mb-1.5">
                            Alamat / Gedung Penempatan Barang
                        </label>
                        <input type="text"
                            x-model="formData.alamat_barang"
                            placeholder="RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1"
                            class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                    </div>

                </div>

            </div>

            <!-- ========================================================================= -->
            <!-- LANGKAH 2: DOKUMEN BAST HIBAH & PEMBERI HIBAH                             -->
            <!-- ========================================================================= -->
            <div x-show="step === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="border-b border-slate-800 pb-4">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-amber-400/10 text-amber-300 border border-amber-400/20 text-xs font-bold mb-2">
                        <span>Langkah 2 dari 3</span>
                    </div>
                    <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                        <span>📜 Dokumen Berita Acara Serah Terima (BAST) Hibah</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Masukkan rincian pihak pemberi hibah dan legalitas BAST yang mendasari penyerahan aset ke RSUD.
                    </p>
                </div>

                <div class="space-y-5">

                    <!-- Pemberi Hibah (Free Text + Quick Tags) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-200 mb-1.5 flex items-center justify-between">
                            <span>Nama Instansi / Lembaga Pemberi Hibah <span class="text-rose-400">*</span></span>
                            <span class="text-[10px] text-slate-400">Teks Bebas / Pihak Ketiga</span>
                        </label>
                        <input type="text"
                            x-model="formData.hibah_pemberi"
                            placeholder="Contoh: Kementerian Kesehatan Republik Indonesia"
                            required
                            class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-amber-400 font-bold">
                        
                        <!-- Quick Suggestions Pills -->
                        <div class="mt-2 flex flex-wrap items-center gap-1.5">
                            <span class="text-[10px] text-slate-500 font-semibold mr-1">Rekomendasi Cepat:</span>
                            <button type="button" @click="formData.hibah_pemberi = 'Kementerian Kesehatan Republik Indonesia'"
                                class="px-2.5 py-1 rounded-lg bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white text-[10px] border border-slate-700 transition-colors">
                                Kemenkes RI
                            </button>
                            <button type="button" @click="formData.hibah_pemberi = 'Dinas Kesehatan Provinsi Jawa Timur'"
                                class="px-2.5 py-1 rounded-lg bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white text-[10px] border border-slate-700 transition-colors">
                                Dinkes Prov. Jatim
                            </button>
                            <button type="button" @click="formData.hibah_pemberi = 'Dinas Kesehatan Kabupaten Bondowoso'"
                                class="px-2.5 py-1 rounded-lg bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white text-[10px] border border-slate-700 transition-colors">
                                Dinkes Kab. Bondowoso
                            </button>
                            <button type="button" @click="formData.hibah_pemberi = 'Pemerintah Provinsi Jawa Timur'"
                                class="px-2.5 py-1 rounded-lg bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white text-[10px] border border-slate-700 transition-colors">
                                Pemprov Jatim
                            </button>
                            <button type="button" @click="formData.hibah_pemberi = 'Donatur Swasta / Yayasan'"
                                class="px-2.5 py-1 rounded-lg bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white text-[10px] border border-slate-700 transition-colors">
                                Swasta / Yayasan
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Nomor BAST -->
                        <div>
                            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                                Nomor BAST Hibah <span class="text-rose-400">*</span>
                            </label>
                            <input type="text"
                                x-model="formData.hibah_nomor_bast"
                                placeholder="Contoh: 020/BAST-HIBAH/RSUD/X/2026"
                                required
                                class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none font-medium">
                            <p class="text-[10px] text-slate-400 mt-1">Nomor Berita Acara Serah Terima resmi.</p>
                        </div>

                        <!-- Tanggal BAST -->
                        <div>
                            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                                Tanggal BAST Hibah <span class="text-rose-400">*</span>
                            </label>
                            <input type="date"
                                x-model="formData.hibah_tanggal_bast"
                                required
                                class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                            <p class="text-[10px] text-slate-400 mt-1">Tanggal penandatanganan dokumen BAST.</p>
                        </div>
                    </div>

                    <!-- Keterangan Tambahan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-200 mb-1.5">
                            Keterangan Tambahan / Riwayat Perolehan
                        </label>
                        <textarea
                            x-model="formData.hibah_keterangan"
                            rows="3"
                            placeholder="Catatan tambahan mengenai kondisi penyerahan, lampiran surat penyerahan, peruntukan aset..."
                            class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl p-3 text-xs text-white focus:outline-none"></textarea>
                    </div>

                </div>

            </div>

            <!-- ========================================================================= -->
            <!-- LANGKAH 3: PREVIEW & KONFIRMASI SIMPAN                                    -->
            <!-- ========================================================================= -->
            <div x-show="step === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="border-b border-slate-800 pb-4">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-400/10 text-emerald-300 border border-emerald-400/20 text-xs font-bold mb-2">
                        <span>Langkah 3 dari 3</span>
                    </div>
                    <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                        <span>🔍 Konfirmasi & Ringkasan Data Hibah</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Pastikan seluruh rincian aset hibah di bawah ini sudah akurat sebelum disimpan ke database SIMAT-RK.
                    </p>
                </div>

                <!-- Review Card Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    
                    <!-- Card Ringkasan Aset -->
                    <div class="p-6 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                            <div class="flex items-center space-x-2">
                                <span class="text-lg">📦</span>
                                <h3 class="text-xs font-bold text-white uppercase tracking-wider">Spesifikasi Aset</h3>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-400/15 text-amber-300 border border-amber-400/30">
                                HIBAH
                            </span>
                        </div>

                        <div class="space-y-2.5 text-xs">
                            <div>
                                <span class="text-slate-400 block text-[11px]">Nama Barang:</span>
                                <span class="font-bold text-white text-sm" x-text="formData.nama_barang || '-'"></span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">Klasifikasi Kode 108:</span>
                                <span class="font-semibold text-amber-300" x-text="selectedSubSub ? (selectedSubSub.kode + ' - ' + selectedSubSub.nama) : '-'"></span>
                            </div>
                            <div class="grid grid-cols-2 gap-3 pt-1">
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Tahun Perolehan:</span>
                                    <span class="font-bold text-white" x-text="formData.tahun_perolehan"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Triwulan:</span>
                                    <span class="font-bold text-white" x-text="formData.triwulan"></span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Kuantitas:</span>
                                    <span class="font-bold text-emerald-400" x-text="formData.jumlah_volume + ' ' + formData.satuan"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Kondisi Fisik:</span>
                                    <span class="font-bold text-white" x-text="formData.kondisi"></span>
                                </div>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">Lokasi Penempatan:</span>
                                <span class="text-slate-200" x-text="formData.alamat_barang || 'RSUD Dr. H. Koesnandi'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Ringkasan Legalitas & Nilai -->
                    <div class="p-6 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-4 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center space-x-2 border-b border-slate-800/80 pb-3 mb-4">
                                <span class="text-lg">📜</span>
                                <h3 class="text-xs font-bold text-white uppercase tracking-wider">Dokumen & Nilai Hibah</h3>
                            </div>

                            <div class="space-y-2.5 text-xs">
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Pemberi Hibah:</span>
                                    <span class="font-bold text-amber-300 text-sm" x-text="formData.hibah_pemberi || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Nomor Dokumen BAST:</span>
                                    <span class="font-mono text-slate-200 font-semibold" x-text="formData.hibah_nomor_bast || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Tanggal BAST:</span>
                                    <span class="text-slate-200 font-semibold" x-text="formatTanggalIndo(formData.hibah_tanggal_bast)"></span>
                                </div>
                                <div x-show="formData.hibah_keterangan">
                                    <span class="text-slate-400 block text-[11px]">Catatan:</span>
                                    <span class="text-slate-300 italic text-[11px]" x-text="formData.hibah_keterangan"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Grand Total Banner -->
                        <div class="mt-4 p-4 rounded-xl bg-gradient-to-r from-amber-400/10 via-amber-400/5 to-slate-900 border border-amber-400/30">
                            <span class="text-[11px] font-bold text-amber-400 uppercase tracking-wider block">
                                Total Nilai Realisasi Hibah:
                            </span>
                            <div class="text-2xl font-black text-amber-300 mt-0.5" x-text="formatRupiah(formData.total_realisasi)"></div>
                            <div class="text-[10px] text-slate-400 mt-1">
                                Nilai Satuan: <span class="font-bold text-slate-300" x-text="formatRupiah(computedHargaSatuan)"></span> / <span x-text="formData.satuan"></span>
                            </div>
                        </div>

                    </div>

                </div>

                <!-- Info Box Registrasi -->
                <div class="p-4 rounded-xl bg-slate-950 border border-emerald-500/20 flex items-center gap-3 text-xs text-slate-400">
                    <span class="text-xl">ℹ️</span>
                    <div>
                        Setelah tombol <span class="font-bold text-emerald-400">"Simpan Data Hibah"</span> ditekan, sistem akan otomatis mendaftarkan aset ke database master ASTAP dengan kategori perolehan <span class="font-bold text-amber-300">HIBAH</span> dan membuat <span class="font-bold text-white" x-text="formData.jumlah_volume"></span> register NIBAR baru yang siap dicetak labelnya.
                    </div>
                </div>

            </div>

            <!-- ========================================================================= -->
            <!-- BOTTOM NAVIGATION BETWEEN STEPS                                          -->
            <!-- ========================================================================= -->
            <div class="pt-6 border-t border-slate-800 flex items-center justify-between">
                
                <div>
                    <button type="button" x-show="step > 1" @click="prevStep()"
                        class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs transition-all flex items-center space-x-2">
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
                        class="px-6 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-extrabold text-xs shadow-lg shadow-amber-400/20 transition-all flex items-center space-x-2">
                        <span>Lanjutkan</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <!-- Final Submit Button -->
                    <button type="submit" x-show="step === 3" :disabled="isSubmitting"
                        class="px-8 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 disabled:opacity-50 text-slate-950 font-black text-xs shadow-xl shadow-emerald-500/25 transition-all flex items-center space-x-2">
                        <span x-show="!isSubmitting">💾 Simpan Data Hibah</span>
                        <span x-show="isSubmitting">Menyimpan...</span>
                    </button>
                </div>

            </div>

        </form>

    </div>

    <!-- Alpine.js Script Implementation -->
    <script>
        function formHibah() {
            return {
                step: 1,
                isSubmitting: false,
                master108: @json($dbMaster108 ?? []),
                
                // Form Model
                formData: {
                    nama_barang: '',
                    jenis_astap_id: '',
                    tahun_perolehan: new Date().getFullYear(),
                    jumlah_volume: 1,
                    satuan: 'Unit',
                    total_realisasi: 0,
                    triwulan: 'TW I',
                    unit_id: '',
                    alamat_barang: 'RSUD Dr. H. Koesnandi Bondowoso',
                    kondisi: 'Baik',
                    hibah_pemberi: '',
                    hibah_nomor_bast: '',
                    hibah_tanggal_bast: new Date().toISOString().split('T')[0],
                    hibah_keterangan: ''
                },

                // 108 Selection State
                selectedJenisIdx: '',
                selectedSubIdx: '',
                selectedSubSub: null,
                currentSubList: [],
                currentSubSubList: [],
                
                // Quick Search State
                search108: '',
                searchResults108: [],
                allFlattened108: [],

                init() {
                    // Set auto triwulan based on current month
                    const m = new Date().getMonth() + 1;
                    if (m >= 1 && m <= 3) this.formData.triwulan = 'TW I';
                    else if (m >= 4 && m <= 6) this.formData.triwulan = 'TW II';
                    else if (m >= 7 && m <= 9) this.formData.triwulan = 'TW III';
                    else this.formData.triwulan = 'TW IV';

                    // Flatten 108 for instant search
                    this.flatten108();
                },

                flatten108() {
                    this.allFlattened108 = [];
                    (this.master108 || []).forEach(j => {
                        (j.subRincian || []).forEach(s => {
                            (s.subSubRincian || []).forEach(ss => {
                                this.allFlattened108.push({
                                    id: ss.id,
                                    kode: ss.kode,
                                    nama: ss.nama,
                                    parentKode: s.kode,
                                    parentNama: s.nama,
                                    jenisKode: j.kode,
                                    jenisNama: j.nama
                                });
                            });
                        });
                    });
                },

                performSearch108() {
                    const q = (this.search108 || '').trim().toLowerCase();
                    if (!q || q.length < 2) {
                        this.searchResults108 = [];
                        return;
                    }
                    this.searchResults108 = this.allFlattened108.filter(item => 
                        item.nama.toLowerCase().includes(q) || item.kode.toLowerCase().includes(q)
                    ).slice(0, 20);
                },

                select108Direct(item) {
                    this.formData.jenis_astap_id = item.id;
                    this.selectedSubSub = item;
                    this.search108 = '';
                    this.searchResults108 = [];

                    // Auto-fill nama_barang if still empty
                    if (!this.formData.nama_barang) {
                        this.formData.nama_barang = item.nama;
                    }

                    // Try to sync cascading indexes
                    const jIdx = this.master108.findIndex(j => j.kode === item.jenisKode);
                    if (jIdx !== -1) {
                        this.selectedJenisIdx = jIdx;
                        this.currentSubList = this.master108[jIdx].subRincian || [];
                        const sIdx = this.currentSubList.findIndex(s => s.kode === item.parentKode);
                        if (sIdx !== -1) {
                            this.selectedSubIdx = sIdx;
                            this.currentSubSubList = this.currentSubList[sIdx].subSubRincian || [];
                        }
                    }
                },

                onJenisChange() {
                    this.currentSubList = [];
                    this.currentSubSubList = [];
                    this.selectedSubIdx = '';
                    this.formData.jenis_astap_id = '';
                    this.selectedSubSub = null;

                    if (this.selectedJenisIdx !== '' && this.master108[this.selectedJenisIdx]) {
                        this.currentSubList = this.master108[this.selectedJenisIdx].subRincian || [];
                    }
                },

                onSubChange() {
                    this.currentSubSubList = [];
                    this.formData.jenis_astap_id = '';
                    this.selectedSubSub = null;

                    if (this.selectedSubIdx !== '' && this.currentSubList[this.selectedSubIdx]) {
                        this.currentSubSubList = this.currentSubList[this.selectedSubIdx].subSubRincian || [];
                    }
                },

                onSubSubChange() {
                    const id = parseInt(this.formData.jenis_astap_id);
                    const found = this.currentSubSubList.find(x => x.id === id);
                    this.selectedSubSub = found || null;
                    if (found && !this.formData.nama_barang) {
                        this.formData.nama_barang = found.nama;
                    }
                },

                clear108Selection() {
                    this.formData.jenis_astap_id = '';
                    this.selectedSubSub = null;
                    this.selectedJenisIdx = '';
                    this.selectedSubIdx = '';
                    this.currentSubList = [];
                    this.currentSubSubList = [];
                },

                get computedHargaSatuan() {
                    const vol = Math.max(1, parseInt(this.formData.jumlah_volume) || 1);
                    const tot = parseFloat(this.formData.total_realisasi) || 0;
                    return tot / vol;
                },

                formatRupiah(val) {
                    const num = parseFloat(val) || 0;
                    return 'Rp ' + Math.round(num).toLocaleString('id-ID');
                },

                formatTanggalIndo(dateStr) {
                    if (!dateStr) return '-';
                    try {
                        const d = new Date(dateStr);
                        return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
                    } catch (e) {
                        return dateStr;
                    }
                },

                goToStep(s) {
                    if (s > this.step) {
                        if (this.validateStep(this.step)) {
                            this.step = s;
                        }
                    } else {
                        this.step = s;
                    }
                },

                nextStep() {
                    if (this.validateStep(this.step)) {
                        this.step++;
                    }
                },

                prevStep() {
                    if (this.step > 1) {
                        this.step--;
                    }
                },

                validateStep(s) {
                    if (s === 1) {
                        if (!this.formData.jenis_astap_id) {
                            alert('⚠️ Mohon pilih Klasifikasi Kode 108 terlebih dahulu.');
                            return false;
                        }
                        if (!this.formData.nama_barang.trim()) {
                            alert('⚠️ Mohon isi Nama Lengkap Barang.');
                            return false;
                        }
                        if (!this.formData.jumlah_volume || this.formData.jumlah_volume < 1) {
                            alert('⚠️ Kuantitas barang minimal 1.');
                            return false;
                        }
                        if (this.formData.total_realisasi === null || this.formData.total_realisasi === '' || this.formData.total_realisasi < 0) {
                            alert('⚠️ Mohon isi Nilai Hibah (Rp).');
                            return false;
                        }
                        return true;
                    } else if (s === 2) {
                        if (!this.formData.hibah_pemberi.trim()) {
                            alert('⚠️ Mohon isi Nama Instansi Pemberi Hibah.');
                            return false;
                        }
                        if (!this.formData.hibah_nomor_bast.trim()) {
                            alert('⚠️ Mohon isi Nomor BAST Hibah.');
                            return false;
                        }
                        if (!this.formData.hibah_tanggal_bast) {
                            alert('⚠️ Mohon tentukan Tanggal BAST Hibah.');
                            return false;
                        }
                        return true;
                    }
                    return true;
                },

                submitForm() {
                    if (!this.validateStep(1) || !this.validateStep(2)) return;
                    
                    this.isSubmitting = true;
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                    fetch("{{ route('astap.store_hibah') }}", {
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
                            alert('🎉 Berhasil! ' + (result.body.message || 'Data Hibah berhasil disimpan.'));
                            window.location.href = result.body.redirect || "{{ route('astap.index') }}";
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
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(245, 158, 11, 0.4);
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(245, 158, 11, 0.7);
        }
    </style>
</x-layout>
