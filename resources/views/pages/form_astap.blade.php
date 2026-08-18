<x-layout :title="request()->routeIs('astap.edit') ? 'Ubah Data ASTAP - SIMAT-RK' : 'Tambah Data ASTAP Baru - SIMAT-RK'">
    @section('page-title', request()->routeIs('astap.edit') ? 'Ubah Data ASTAP' : 'Tambah Data ASTAP Baru')
    @section('breadcrumb', request()->routeIs('astap.edit') ? 'Master Utama / Data ASTAP / Ubah Data' : 'Master Utama / Data ASTAP / Tambah Baru')

    <div x-data="{
        isEdit: {{ request()->routeIs('astap.edit') ? 'true' : 'false' }},
        currentStep: 1,
        totalSteps: 4,

        formData: {
            category: 'KIB B',
            jenis_aset_kode: '1.3.2',
            jenis_aset_nama: 'PERALATAN DAN MESIN',
            sub_rincian_kode: '1.3.2.02.01.01',
            sub_rincian_nama: 'ALAT KEDOKTERAN KESEHATAN MATRA',
            kode_barang: '1.3.2.02.01.01.005',
            nama_barang: 'CT-Scan 128 Slice High Resolution',
            volume_satuan: '1 Unit (128-Slice System)',
            
            // Blok 1: Penganggaran & Akuntansi
            program_kode: '0.00.01',
            program_nama: 'Program Pengadaan Sarana & Prasarana Kesehatan RSUD',
            kegiatan_kode: '0.00.01.2.10',
            kegiatan_nama: 'Pengembangan Fasilitas Radiologi & Diagnostik',
            sub_kegiatan_kode: '0.00.01.2.10.0001',
            sub_kegiatan_nama: 'Pengadaan Alat Kedokteran Radiologi',
            rekening_kode: '5.2.02.02.01.0005',
            rekening_nama: 'Belanja Modal Peralatan Kedokteran Radiologi',
            jumlah_anggaran: 'Rp 1.500.000.000',
            jumlah_realisasi: 'Rp 1.450.000.000',
            
            // Blok 2: Dokumen Pembelian & Pengadaan
            spk_nomor: '045/SPK-RAD/VII/2024',
            spk_tanggal: '2024-07-10',
            surat_pesanan_nomor: '045/RSUD/VII/2024',
            surat_pesanan_tanggal: '2024-07-15',
            kwitansi_nomor: 'KW-045/RAD/2024',
            kwitansi_tanggal: '2024-08-01',
            faktur_nomor: 'INV-RAD-2024-01',
            faktur_tanggal: '2024-08-05',
            
            // Blok 3: Spesifikasi Teknis
            luas_tanah: '',
            tahun_pengadaan: '2024',
            letak_lokasi: 'Gedung Radiologi Lt. 1',
            hak_tanah: 'Hak Pakai',
            sertifikat_nomor: '',
            sertifikat_tanggal: '',
            penggunaan: 'Pemeriksaan Tomografi Radiologi Pasien',
            asal_usul: 'DAK Kesehatan',
            harga_satuan: 'Rp 1.450.000.000',
            kondisi: 'Baik',
            keterangan: 'Telah terkalibrasi BAPETEN dan siap operasional 24 Jam',
            
            // Non-tanah extras
            merk: 'Siemens SOMATOM',
            type: '128-Slice Perspective',
            ukuran: 'Unit Radiologi Medis Lengkap',
            bahan: 'Logam & Elektronik Medis',
            no_pabrik: 'SN-99812-RAD'
        },

        init() {
            if (!this.isEdit) {
                this.formData = {
                    category: 'KIB A',
                    jenis_aset_kode: '1.3.1',
                    jenis_aset_nama: 'TANAH',
                    sub_rincian_kode: '1.3.1.01.01.01',
                    sub_rincian_nama: 'TANAH BANGUNAN PERUMAHAN/G.TEMPAT TINGGAL',
                    kode_barang: '1.3.1.01.01.01.001',
                    nama_barang: '',
                    volume_satuan: '1,5 Hektar (15.000 m²)',
                    program_kode: '0.00.01',
                    program_nama: 'Program Penunjang Urusan Pemerintah Daerah Kabupaten/Kota',
                    kegiatan_kode: '0.00.01.2.10',
                    kegiatan_nama: 'Peningkatan Pelayanan BLUD',
                    sub_kegiatan_kode: '0.00.01.2.10.0001',
                    sub_kegiatan_nama: 'Pelayanan dan Penunjang Pelayanan BLUD',
                    rekening_kode: '5.2.02.01.01.0001',
                    rekening_nama: 'Belanja Modal Tanah',
                    jumlah_anggaran: '',
                    jumlah_realisasi: '',
                    spk_nomor: '',
                    spk_tanggal: '',
                    surat_pesanan_nomor: '',
                    surat_pesanan_tanggal: '',
                    kwitansi_nomor: '',
                    kwitansi_tanggal: '',
                    faktur_nomor: '',
                    faktur_tanggal: '',
                    luas_tanah: '',
                    tahun_pengadaan: new Date().getFullYear(),
                    letak_lokasi: '',
                    hak_tanah: 'Hak Pakai',
                    sertifikat_nomor: '',
                    sertifikat_tanggal: '',
                    penggunaan: 'Fasilitas Layanan Kesehatan RSUD',
                    asal_usul: 'APBD Kabupaten',
                    harga_satuan: '',
                    kondisi: 'Baik',
                    keterangan: '',
                    merk: '',
                    type: '',
                    ukuran: '',
                    bahan: '',
                    no_pabrik: ''
                };
            }
        },

        selectCategory(kib, kode, nama) {
            this.formData.category = kib;
            this.formData.jenis_aset_kode = kode;
            this.formData.jenis_aset_nama = nama;
        },

        submitForm() {
            alert('✅ Data ASTAP (' + this.formData.nama_barang + ') berhasil disimpan ke database SIMAT-RK!');
            window.location.href = '{{ route('astap.index') }}';
        }
    }" x-cloak class="space-y-6">

        <!-- Top Navigation Bar (Back + Save Actions) -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl">
            <div class="flex items-center space-x-4">
                <a href="{{ route('astap.index') }}" 
                   class="w-10 h-10 rounded-2xl bg-slate-950 border border-slate-800 hover:border-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <div class="inline-flex items-center space-x-2 px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[10px] font-bold mb-1">
                        <span x-text="isEdit ? '✏️ MODE EDIT DATA ASTAP' : '📝 FORM PENAMBAHAN DATA ASTAP'"></span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight" x-text="isEdit ? 'Ubah Data ASTAP: ' + formData.nama_barang : 'Input Penambahan Aset Tetap (ASTAP)'"></h1>
                </div>
            </div>

            <div class="flex items-center space-x-3 w-full sm:w-auto justify-end">
                <a href="{{ route('astap.index') }}" 
                   class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-all">
                    Batal
                </a>
                <button type="button" @click="submitForm()"
                        class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span x-text="isEdit ? 'Simpan Perubahan Aset' : 'Simpan Data ASTAP Lengkap'"></span>
                </button>
            </div>
        </div>

        <!-- Multi-Step Stepper Header -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                
                <!-- Step 1 Tab -->
                <button type="button" @click="currentStep = 1" class="text-left group cursor-pointer">
                    <div class="flex items-center space-x-3 mb-2">
                        <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all"
                             :class="currentStep === 1 ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/30' : (currentStep > 1 ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : 'bg-slate-950 text-slate-500 border border-slate-800')">
                            <span x-show="currentStep <= 1">1</span>
                            <span x-show="currentStep > 1">✓</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider block" :class="currentStep === 1 ? 'text-emerald-400' : 'text-slate-500'">Langkah 1</span>
                            <span class="text-xs font-bold text-white block">Jenis KIB & Kode 108</span>
                        </div>
                    </div>
                    <div class="h-1.5 rounded-full w-full transition-all" :class="currentStep >= 1 ? 'bg-emerald-500' : 'bg-slate-950'"></div>
                </button>

                <!-- Step 2 Tab -->
                <button type="button" @click="currentStep = 2" class="text-left group cursor-pointer">
                    <div class="flex items-center space-x-3 mb-2">
                        <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all"
                             :class="currentStep === 2 ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/30' : (currentStep > 2 ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : 'bg-slate-950 text-slate-500 border border-slate-800')">
                            <span x-show="currentStep <= 2">2</span>
                            <span x-show="currentStep > 2">✓</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider block" :class="currentStep === 2 ? 'text-emerald-400' : 'text-slate-500'">Langkah 2</span>
                            <span class="text-xs font-bold text-white block">Penganggaran DPA</span>
                        </div>
                    </div>
                    <div class="h-1.5 rounded-full w-full transition-all" :class="currentStep >= 2 ? 'bg-emerald-500' : 'bg-slate-950'"></div>
                </button>

                <!-- Step 3 Tab -->
                <button type="button" @click="currentStep = 3" class="text-left group cursor-pointer">
                    <div class="flex items-center space-x-3 mb-2">
                        <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all"
                             :class="currentStep === 3 ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/30' : (currentStep > 3 ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : 'bg-slate-950 text-slate-500 border border-slate-800')">
                            <span x-show="currentStep <= 3">3</span>
                            <span x-show="currentStep > 3">✓</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider block" :class="currentStep === 3 ? 'text-emerald-400' : 'text-slate-500'">Langkah 3</span>
                            <span class="text-xs font-bold text-white block">Dokumen Pembelian</span>
                        </div>
                    </div>
                    <div class="h-1.5 rounded-full w-full transition-all" :class="currentStep >= 3 ? 'bg-emerald-500' : 'bg-slate-950'"></div>
                </button>

                <!-- Step 4 Tab -->
                <button type="button" @click="currentStep = 4" class="text-left group cursor-pointer">
                    <div class="flex items-center space-x-3 mb-2">
                        <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all"
                             :class="currentStep === 4 ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/30' : 'bg-slate-950 text-slate-500 border border-slate-800'">
                            <span>4</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider block" :class="currentStep === 4 ? 'text-emerald-400' : 'text-slate-500'">Langkah 4</span>
                            <span class="text-xs font-bold text-white block">Spesifikasi KIB Fisik</span>
                        </div>
                    </div>
                    <div class="h-1.5 rounded-full w-full transition-all" :class="currentStep >= 4 ? 'bg-emerald-500' : 'bg-slate-950'"></div>
                </button>

            </div>
        </div>

        <!-- Main Form Container -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl">
            
            <!-- ========================================================================= -->
            <!-- STEP 1: KATEGORI KIB & KODE 108 BMD                                       -->
            <!-- ========================================================================= -->
            <div x-show="currentStep === 1" class="space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                        <span class="p-2 rounded-xl bg-emerald-500/10 text-emerald-400 text-sm">🌾</span>
                        <span>Langkah 1: Klasifikasi Kategori KIB & Kode 108 Permendagri</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">Pilih kategori jenis utama aset tetap di bawah ini:</p>
                </div>

                <!-- 8 Kategori KIB Cards -->
                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-2.5">
                    <!-- KIB A: Aset Tanah -->
                    <div @click="selectCategory('KIB A', '1.3.1', 'Aset Tanah')" 
                         class="p-3.5 rounded-2xl border cursor-pointer transition-all flex flex-col justify-between"
                         :class="formData.category === 'KIB A' ? 'bg-amber-500/15 border-amber-500 ring-2 ring-amber-500/30' : 'bg-slate-950/60 border-slate-800 hover:border-slate-700'">
                        <div class="text-xl mb-1.5">🌾</div>
                        <div>
                            <span class="text-[9px] font-bold text-amber-400">1.3.1</span>
                            <h3 class="text-xs font-bold text-white leading-tight">KIB A</h3>
                            <p class="text-[9px] text-slate-400 mt-0.5">Aset Tanah</p>
                        </div>
                    </div>

                    <!-- KIB B: Aset Peralatan dan Mesin -->
                    <div @click="selectCategory('KIB B', '1.3.2', 'Aset Peralatan dan Mesin')" 
                         class="p-3.5 rounded-2xl border cursor-pointer transition-all flex flex-col justify-between"
                         :class="formData.category === 'KIB B' ? 'bg-cyan-500/15 border-cyan-500 ring-2 ring-cyan-500/30' : 'bg-slate-950/60 border-slate-800 hover:border-slate-700'">
                        <div class="text-xl mb-1.5">🔬</div>
                        <div>
                            <span class="text-[9px] font-bold text-cyan-400">1.3.2</span>
                            <h3 class="text-xs font-bold text-white leading-tight">KIB B</h3>
                            <p class="text-[9px] text-slate-400 mt-0.5">Peralatan & Mesin</p>
                        </div>
                    </div>

                    <!-- KIB C: Aset Gedung & Bangunan -->
                    <div @click="selectCategory('KIB C', '1.3.3', 'Aset Gedung & Bangunan')" 
                         class="p-3.5 rounded-2xl border cursor-pointer transition-all flex flex-col justify-between"
                         :class="formData.category === 'KIB C' ? 'bg-purple-500/15 border-purple-500 ring-2 ring-purple-500/30' : 'bg-slate-950/60 border-slate-800 hover:border-slate-700'">
                        <div class="text-xl mb-1.5">🏢</div>
                        <div>
                            <span class="text-[9px] font-bold text-purple-400">1.3.3</span>
                            <h3 class="text-xs font-bold text-white leading-tight">KIB C</h3>
                            <p class="text-[9px] text-slate-400 mt-0.5">Gedung & Bangunan</p>
                        </div>
                    </div>

                    <!-- KIB D: Aset Jalan, Irigasi dan Jaringan -->
                    <div @click="selectCategory('KIB D', '1.3.4', 'Aset Jalan, Irigasi dan Jaringan')" 
                         class="p-3.5 rounded-2xl border cursor-pointer transition-all flex flex-col justify-between"
                         :class="formData.category === 'KIB D' ? 'bg-teal-500/15 border-teal-500 ring-2 ring-teal-500/30' : 'bg-slate-950/60 border-slate-800 hover:border-slate-700'">
                        <div class="text-xl mb-1.5">🚰</div>
                        <div>
                            <span class="text-[9px] font-bold text-teal-400">1.3.4</span>
                            <h3 class="text-xs font-bold text-white leading-tight">KIB D</h3>
                            <p class="text-[9px] text-slate-400 mt-0.5">Jalan & Jaringan</p>
                        </div>
                    </div>

                    <!-- KIB E: Aset Tetap Lainnya -->
                    <div @click="selectCategory('KIB E', '1.3.5', 'Aset Tetap Lainnya')" 
                         class="p-3.5 rounded-2xl border cursor-pointer transition-all flex flex-col justify-between"
                         :class="formData.category === 'KIB E' ? 'bg-orange-500/15 border-orange-500 ring-2 ring-orange-500/30' : 'bg-slate-950/60 border-slate-800 hover:border-slate-700'">
                        <div class="text-xl mb-1.5">📦</div>
                        <div>
                            <span class="text-[9px] font-bold text-orange-400">1.3.5</span>
                            <h3 class="text-xs font-bold text-white leading-tight">KIB E</h3>
                            <p class="text-[9px] text-slate-400 mt-0.5">Aset Tetap Lainnya</p>
                        </div>
                    </div>

                    <!-- KIB F: Aset Konstruksi Dalam Pengerjaan (KDP) -->
                    <div @click="selectCategory('KIB F', '1.3.6', 'Aset Konstruksi Dalam Pengerjaan')" 
                         class="p-3.5 rounded-2xl border cursor-pointer transition-all flex flex-col justify-between"
                         :class="formData.category === 'KIB F' ? 'bg-rose-500/15 border-rose-500 ring-2 ring-rose-500/30' : 'bg-slate-950/60 border-slate-800 hover:border-slate-700'">
                        <div class="text-xl mb-1.5">🏗️</div>
                        <div>
                            <span class="text-[9px] font-bold text-rose-400">1.3.6</span>
                            <h3 class="text-xs font-bold text-white leading-tight">KIB F</h3>
                            <p class="text-[9px] text-slate-400 mt-0.5">Konstruksi (KDP)</p>
                        </div>
                    </div>

                    <!-- KIB G: Aset Tidak Berwujud -->
                    <div @click="selectCategory('KIB G', '1.5.3', 'Aset Tidak Berwujud')" 
                         class="p-3.5 rounded-2xl border cursor-pointer transition-all flex flex-col justify-between"
                         :class="formData.category === 'KIB G' ? 'bg-indigo-500/15 border-indigo-500 ring-2 ring-indigo-500/30' : 'bg-slate-950/60 border-slate-800 hover:border-slate-700'">
                        <div class="text-xl mb-1.5">💾</div>
                        <div>
                            <span class="text-[9px] font-bold text-indigo-400">1.5.3</span>
                            <h3 class="text-xs font-bold text-white leading-tight">KIB G</h3>
                            <p class="text-[9px] text-slate-400 mt-0.5">Tidak Berwujud</p>
                        </div>
                    </div>

                    <!-- KIB H: Aset Tetap Dalam Renovasi -->
                    <div @click="selectCategory('KIB H', '1.3.7', 'Aset Tetap Dalam Renovasi')" 
                         class="p-3.5 rounded-2xl border cursor-pointer transition-all flex flex-col justify-between"
                         :class="formData.category === 'KIB H' ? 'bg-pink-500/15 border-pink-500 ring-2 ring-pink-500/30' : 'bg-slate-950/60 border-slate-800 hover:border-slate-700'">
                        <div class="text-xl mb-1.5">🔨</div>
                        <div>
                            <span class="text-[9px] font-bold text-pink-400">1.3.7</span>
                            <h3 class="text-xs font-bold text-white leading-tight">KIB H</h3>
                            <p class="text-[9px] text-slate-400 mt-0.5">Dalam Renovasi</p>
                        </div>
                    </div>
                </div>

                <!-- Sub Rincian Objek & Kode 108 Selector -->
                <div class="p-6 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold text-white uppercase tracking-wider flex items-center space-x-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            <span>Filter Sub Rincian Objek Kode 108 BMD</span>
                        </h4>
                        <span class="text-[11px] font-mono text-emerald-400" x-text="'Kategori Aktif: ' + formData.category + ' (' + formData.jenis_aset_nama + ')'"></span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-slate-300 font-semibold text-xs mb-1.5">Pilih Sub Rincian Objek (Level 5)</label>
                            <select x-model="formData.sub_rincian_nama" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-emerald-500">
                                <template x-if="formData.category === 'KIB A'">
                                    <option value="TANAH BANGUNAN PERUMAHAN/G.TEMPAT TINGGAL">1.3.1.01.01.01 - TANAH BANGUNAN RUMAH/GEDUNG TINGGAL</option>
                                </template>
                                <template x-if="formData.category === 'KIB A'">
                                    <option value="TANAH UNTUK BANGUNAN GED.PERDAGANGAN/PERUSAHAAN">1.3.1.01.01.02 - TANAH GEDUNG RSUD & FASILITAS</option>
                                </template>
                                <template x-if="formData.category === 'KIB B'">
                                    <option value="ALAT KEDOKTERAN UMUM & RADIOLOGI">1.3.2.02.01.01 - ALAT KEDOKTERAN UMUM & RADIOLOGI</option>
                                </template>
                                <template x-if="formData.category === 'KIB B'">
                                    <option value="ALAT KEDOKTERAN ICU / MONITOR">1.3.2.02.01.02 - ALAT KEDOKTERAN ICU & MONITOR PASIEN</option>
                                </template>
                                <template x-if="formData.category === 'KIB C'">
                                    <option value="BANGUNAN GEDUNG RUMAH SAKIT">1.3.3.01.01.01 - BANGUNAN GEDUNG RAWAT INAP & POLI</option>
                                </template>
                                <template x-if="formData.category === 'KIB D'">
                                    <option value="INSTALASI PIPA GAS & OKSIGEN MEDIS">1.3.4.03.01.01 - JARINGAN PIPA OKSIGEN MEDIS & AIR</option>
                                </template>
                                <template x-if="formData.category === 'KIB E'">
                                    <option value="BUKU DAN LITERATUR KEDOKTERAN">1.3.5.01.01.01 - BUKU LITERATUR KEDOKTERAN & PERPUSTAKAAN</option>
                                </template>
                                <template x-if="formData.category === 'KIB F'">
                                    <option value="KONSTRUKSI DALAM PENGERJAAN">1.3.6.01.01.01 - BANGUNAN KDP GEDUNG/RUANGAN BARU</option>
                                </template>
                                <template x-if="formData.category === 'KIB G'">
                                    <option value="SOFTWARE / SISTEM INFORMASI SIMRS">1.5.3.01.01.01 - ASET TIDAK BERWUJUD (SOFTWARE / LISENSI)</option>
                                </template>
                                <template x-if="formData.category === 'KIB H'">
                                    <option value="ASET TETAP DALAM RENOVASI">1.3.7.01.01.01 - REHABILITASI & RENOVASI GEDUNG RSUD</option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label class="block text-slate-300 font-semibold text-xs mb-1.5">Kodefikasi 108 Spesifik (Level 6)</label>
                            <input type="text" x-model="formData.kode_barang" placeholder="Contoh: 1.3.2.02.01.01.005" 
                                   class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-xs text-emerald-400 font-mono font-bold focus:outline-none focus:border-emerald-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- STEP 2: BLOK PENGANGGARAN & AKUNTANSI                                     -->
            <!-- ========================================================================= -->
            <div x-show="currentStep === 2" class="space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                        <span class="p-2 rounded-xl bg-emerald-500/10 text-emerald-400 text-sm">📑</span>
                        <span>Langkah 2: Blok Penganggaran & Akuntansi (DPA BLUD / APBD)</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">Rincian kode program, kegiatan, sub kegiatan, rekening belanja, dan nilai realisasi:</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Kode Program</label>
                        <input type="text" x-model="formData.program_kode" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white font-mono">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Nama Program</label>
                        <input type="text" x-model="formData.program_nama" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Kode Kegiatan</label>
                        <input type="text" x-model="formData.kegiatan_kode" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white font-mono">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Nama Kegiatan</label>
                        <input type="text" x-model="formData.kegiatan_nama" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Kode Sub Kegiatan</label>
                        <input type="text" x-model="formData.sub_kegiatan_kode" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white font-mono">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Nama Sub Kegiatan</label>
                        <input type="text" x-model="formData.sub_kegiatan_nama" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Kode Rekening Belanja</label>
                        <input type="text" x-model="formData.rekening_kode" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white font-mono">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Uraian Rekening Belanja Modal</label>
                        <input type="text" x-model="formData.rekening_nama" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-800">
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Jumlah Anggaran DPA (Rp)</label>
                        <input type="text" x-model="formData.jumlah_anggaran" placeholder="Rp 1.500.000.000" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white font-mono">
                    </div>
                    <div>
                        <label class="block text-emerald-400 font-bold text-xs mb-1.5">Jumlah Realisasi Anggaran (Rp)</label>
                        <input type="text" x-model="formData.jumlah_realisasi" placeholder="Rp 1.450.000.000" class="w-full bg-slate-950 border border-emerald-500/50 rounded-xl px-4 py-3 text-xs text-emerald-300 font-mono font-extrabold focus:outline-none focus:border-emerald-400">
                    </div>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- STEP 3: BLOK DOKUMEN PEMBELIAN & PENGADAAN                                -->
            <!-- ========================================================================= -->
            <div x-show="currentStep === 3" class="space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                        <span class="p-2 rounded-xl bg-purple-500/10 text-purple-400 text-sm">🧾</span>
                        <span>Langkah 3: Blok Dokumen Pembelian & Bukti Transaksi</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">Lengkapi nomor dan tanggal surat pesanan, kontrak SPK, kwitansi, dan faktur:</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Nomor SPK / Surat Perjanjian Kontrak</label>
                        <input type="text" x-model="formData.spk_nomor" placeholder="045/SPK-MED/2026" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Tanggal SPK / Kontrak</label>
                        <input type="date" x-model="formData.spk_tanggal" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Nomor Surat Pesanan (Order Form)</label>
                        <input type="text" x-model="formData.surat_pesanan_nomor" placeholder="019/BN.BA/VI/2025" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Tanggal Surat Pesanan</label>
                        <input type="date" x-model="formData.surat_pesanan_tanggal" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Nomor Kwitansi Pembayaran</label>
                        <input type="text" x-model="formData.kwitansi_nomor" placeholder="KW-092/RSUD/2026" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Tanggal Kwitansi</label>
                        <input type="date" x-model="formData.kwitansi_tanggal" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Nomor Faktur / Tax Invoice</label>
                        <input type="text" x-model="formData.faktur_nomor" placeholder="INV-2026/088" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Tanggal Faktur</label>
                        <input type="date" x-model="formData.faktur_tanggal" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                    </div>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- STEP 4: BLOK SPESIFIKASI TEKNIS KIB                                       -->
            <!-- ========================================================================= -->
            <div x-show="currentStep === 4" class="space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                        <span class="p-2 rounded-xl bg-teal-500/10 text-teal-400 text-sm">📏</span>
                        <span>Langkah 4: Identitas Fisik & Kuantitas / Luas Aset</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">Lengkapi nama barang, besaran volume/luas (misal hektar/unit/m²), dan spesifikasi detail KIB:</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-200 font-bold text-xs mb-1.5">Nama Barang / ASTAP</label>
                        <input type="text" x-model="formData.nama_barang" placeholder="Contoh: CT-Scan 128 Slice / Tanah Bangunan RSUD" 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white font-bold focus:outline-none focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-teal-400 font-bold text-xs mb-1.5">Volume / Kuantitas / Luas Aset</label>
                        <input type="text" x-model="formData.volume_satuan" placeholder="Contoh: 1,5 Hektar / 2 Unit / 1.200 m² / 500 Meter" 
                               class="w-full bg-slate-950 border border-teal-500/40 rounded-xl px-4 py-3 text-xs text-teal-200 font-semibold focus:outline-none focus:border-teal-400">
                    </div>
                </div>

                <!-- SPESIFIKASI KHUSUS JIKA KIB A (TANAH) -->
                <template x-if="formData.category === 'KIB A'">
                    <div class="space-y-4 p-5 rounded-2xl bg-amber-500/5 border border-amber-500/20">
                        <h4 class="text-xs font-bold text-amber-300 uppercase tracking-wider">Spesifikasi Khusus KIB A (Tanah & Lahan)</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-slate-400 text-xs mb-1">Luas Tanah (M²)</label>
                                <input type="text" x-model="formData.luas_tanah" placeholder="35.400 m²" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-xs mb-1">Tahun Pengadaan</label>
                                <input type="text" x-model="formData.tahun_pengadaan" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-xs mb-1">Hak / Status Tanah</label>
                                <select x-model="formData.hak_tanah" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white">
                                    <option>Hak Pakai</option>
                                    <option>Hak Milik</option>
                                    <option>Hak Pengelolaan</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-slate-400 text-xs mb-1">Nomor Sertifikat</label>
                                <input type="text" x-model="formData.sertifikat_nomor" placeholder="HP-108/RSUD/1984" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-xs mb-1">Tanggal Sertifikat</label>
                                <input type="date" x-model="formData.sertifikat_tanggal" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white">
                            </div>
                        </div>

                        <div>
                            <label class="block text-slate-400 text-xs mb-1">Letak / Alamat / Lokasi Tanah</label>
                            <input type="text" x-model="formData.letak_lokasi" placeholder="Jl. Kapten Pierre Tendean No. 3, Bondowoso" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white">
                        </div>
                    </div>
                </template>

                <!-- SPESIFIKASI KHUSUS JIKA KIB B/C/D/E/F -->
                <template x-if="formData.category !== 'KIB A'">
                    <div class="space-y-4 p-5 rounded-2xl bg-cyan-500/5 border border-cyan-500/20">
                        <h4 class="text-xs font-bold text-cyan-300 uppercase tracking-wider">Spesifikasi Fisik & Teknis Barang</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-slate-400 text-xs mb-1">Merk / Brand</label>
                                <input type="text" x-model="formData.merk" placeholder="Siemens / Terumo..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-xs mb-1">Type / Model</label>
                                <input type="text" x-model="formData.type" placeholder="128-Slice / uMEC-12..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-xs mb-1">Bahan / Material</label>
                                <input type="text" x-model="formData.bahan" placeholder="Campuran Logam & Baja..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-slate-400 text-xs mb-1">Ukuran / Dimensi / Kapasitas</label>
                                <input type="text" x-model="formData.ukuran" placeholder="Ukuran / kapasitas..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-xs mb-1">No. Pabrik / No. Rangka / Serial Number</label>
                                <input type="text" x-model="formData.no_pabrik" placeholder="SN-XXXXX" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white font-mono">
                            </div>
                        </div>
                    </div>
                </template>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Tahun Masuk / Pengadaan</label>
                        <input type="text" x-model="formData.tahun_pengadaan" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white font-mono">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Kondisi Barang Terkini</label>
                        <select x-model="formData.kondisi" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                            <option value="Baik">Baik (B)</option>
                            <option value="Rusak Ringan">Rusak Ringan (RR)</option>
                            <option value="Rusak Berat">Rusak Berat (RB)</option>
                            <option value="Dalam Renovasi">Dalam Renovasi</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Asal Usul Perolehan</label>
                        <select x-model="formData.asal_usul" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                            <option>BLUD RSUD</option>
                            <option>APBD Kabupaten</option>
                            <option>DAK Kesehatan</option>
                            <option>Hibah Pemerintah</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Catatan / Keterangan Tambahan</label>
                    <textarea x-model="formData.keterangan" rows="2" placeholder="Keterangan kondisi atau dokumen pendukung..." 
                              class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-emerald-500"></textarea>
                </div>
            </div>

            <!-- Bottom Navigation Between Steps -->
            <div class="pt-8 mt-8 border-t border-slate-800 flex items-center justify-between">
                <div>
                    <button type="button" x-show="currentStep > 1" @click="currentStep--"
                            class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-all flex items-center space-x-2">
                        <span>&larr; Langkah Sebelumnya</span>
                    </button>
                </div>

                <div class="flex items-center space-x-3">
                    <a href="{{ route('astap.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-all">
                        Batal
                    </a>

                    <!-- Next Step Button -->
                    <button type="button" x-show="currentStep < totalSteps" @click="currentStep++"
                            class="px-6 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-2">
                        <span>Lanjut Langkah <span x-text="currentStep + 1"></span> &rarr;</span>
                    </button>

                    <!-- Submit Button -->
                    <button type="button" x-show="currentStep === totalSteps" @click="submitForm()"
                            class="px-6 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-emerald-500/30 transition-all flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span x-text="isEdit ? 'Simpan Perubahan' : 'Simpan Data ASTAP Lengkap'"></span>
                    </button>
                </div>
            </div>

        </div>

    </div>
</x-layout>
