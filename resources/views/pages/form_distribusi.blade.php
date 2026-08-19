<x-layout :title="request()->routeIs('distribusi.edit') ? 'Ubah Distribusi ASTAP - SIMAT-RK' : 'Input Distribusi Baru - SIMAT-RK'">
    @section('page-title', request()->routeIs('distribusi.edit') ? 'Ubah Distribusi ASTAP' : 'Input Distribusi Baru')
    @section('breadcrumb', request()->routeIs('distribusi.edit') ? 'Master Utama / Distribusi ASTAP / Ubah' : 'Master Utama / Distribusi ASTAP / Input Baru')

    <div x-data="{
        isEdit: {{ request()->routeIs('distribusi.edit') ? 'true' : 'false' }},
        
        // 1. Autocomplete Search Barang State
        barangSearch: '',
        isSearchingBarang: false,
        selectedBarangObj: null,

        // 2. Autocomplete Search Unit / Paviliun State
        unitSearch: '',
        isSearchingUnit: false,
        selectedUnitObj: null,

        // Database Katalog Barang ASTAP RSUD
        katalogAstap: [
            { kode: '1.3.2.05.02.01.004', nama: 'Ember Plastik Tertutup Medis / Non-Medis (50 Liter)', kategori: 'Peralatan Sanitasi & Kebersihan', merk: 'Clio Plastik / Lion Star 50L', satuan: 'Unit' },
            { kode: '1.3.2.02.01.04.012', nama: 'Ember Stainless Steel Medis + Pedal Tutup', kategori: 'Alat Kedokteran & Bedah', merk: 'Maru Stainless Medical Grade', satuan: 'Unit' },
            { kode: '1.3.2.02.01.01.025', nama: 'Emergency Trolley Kit Lengkap IGD / ICU', kategori: 'Alat Kedokteran Gawat Darurat', merk: 'Paramount Emergency Crash Cart', satuan: 'Unit' },
            { kode: '1.3.2.02.01.08.001', nama: 'Bed Patient Electric 3 Crank Acare', kategori: 'Perlengkapan Kamar Rawat Inap', merk: 'Acare Electric Medical Bed', satuan: 'Unit' },
            { kode: '1.3.2.05.01.04.008', nama: 'Kasur Matras spoon (Mattress Foam Adult 200x90x10)', kategori: 'Perlengkapan Kamar Pasien', merk: 'Mattres Cover Spon FO R.Inap', satuan: 'Unit' },
            { kode: '1.3.2.01.03.05.010', nama: 'Pompa Air Shimizu PS-130 BIT', kategori: 'Peralatan Mesin & Sarpras', merk: 'Shimizu PS-130 BIT Otomatis', satuan: 'Unit' },
            { kode: '1.3.2.01.03.05.005', nama: 'Submersible Pump 7.5 HP Franklin Electric', kategori: 'Peralatan Mesin & Sanitasi', merk: 'Franklin Electric 3Phase 7.5HP', satuan: 'Unit' },
            { kode: '1.3.2.02.01.01.002', nama: 'USG 4D Color Doppler Imaging Unit Mindray', kategori: 'Alat Diagnostik Radiologi & Imaging', merk: 'Mindray Resona 7 Ultrasound', satuan: 'Unit' },
            { kode: '1.3.2.02.01.05.001', nama: 'CT-Scan 128 Slice High Resolution Canon Aquilion', kategori: 'Alat Radiologi Canggih', merk: 'Canon Aquilion Lightning 128', satuan: 'Unit' },
            { kode: '1.3.2.02.01.01.015', nama: 'Defibrillator Biphasic Monitor Mindray BeneHeart D3', kategori: 'Emergency Kit & Resusitasi', merk: 'Mindray BeneHeart D3 Biphasic', satuan: 'Unit' },
            { kode: '1.3.2.10.01.02.003', nama: 'Laptop Operasional SIMAT-RK ASUS ExpertBook Core i7', kategori: 'Peralatan Komputer & IT', merk: 'ASUS ExpertBook B1402CBA', satuan: 'Unit' },
            { kode: '1.3.2.10.02.01.005', nama: 'Printer Thermal Resep & Label Laboratorium', kategori: 'Peralatan IT & Farmasi', merk: 'Epson TM-T82X Thermal Auto-Cutter', satuan: 'Unit' },
            { kode: '1.3.2.05.01.01.012', nama: 'Kursi Tunggu Stainless 4 Dudukan Ruang Poli', kategori: 'Mebelair & Sarana Pasien', merk: 'Indachi Stainless Steel 4-Seater', satuan: 'Unit' },
            { kode: '1.3.2.05.01.02.006', nama: 'Lemari Obat Kaca 2 Pintu Farmasi Rawat Inap', kategori: 'Mebelair Medis & Farmasi', merk: 'Baja Coating Glass Door Cabinet', satuan: 'Unit' },
            { kode: '1.3.2.02.01.04.005', nama: 'Meja Tindakan Stainless Steel IGD', kategori: 'Alat Medis & Tindakan', merk: 'Stainless 304 Examination Table', satuan: 'Unit' },
            { kode: '1.3.2.02.01.01.018', nama: 'Tensimeter Digital Stand Mobile Riester', kategori: 'Alat Diagnostik & TTV', merk: 'Riester Ri-Champion Mobile Stand', satuan: 'Unit' },
            { kode: '1.3.2.02.01.02.007', nama: 'Suction Pump Portable Medis Thomas', kategori: 'Alat Penghisap Lendir Medis', merk: 'Thomas 1632 Aspirator Heavy Duty', satuan: 'Unit' },
            { kode: '1.3.2.02.01.03.002', nama: 'Incubator Bayi Transport Medis', kategori: 'Alat Perinatologi & Bayi', merk: 'Ningbo David YP-100 Transport', satuan: 'Unit' },
            { kode: '1.3.2.02.01.06.004', nama: 'Tabung Oksigen Medis 6m3 + Regulator Flowmeter', kategori: 'Gas Medis & Resusitasi', merk: 'Samator Medical Gas Cylinder 6m3', satuan: 'Unit' },
            { kode: '1.3.2.02.01.07.001', nama: 'Lampu Operasi LED Ceiling Double Dome', kategori: 'Peralatan Bedah Sentral', merk: 'Mindray HyLED 8600 Surgical Light', satuan: 'Unit' }
        ],

        // Database Master Unit & Pegawai Penerima (Sub Admin Terdaftar langsung dari Tabel Units)
        unitList: {{ Js::from($units ?? []) }},

        // Data Form Distribusi
        formData: {
            kode: 'DST-2026-004',
            nama: 'Bed Patient Electric 3 Crank Acare',
            kode_barang: '1.3.2.02.01.08.001',
            tujuan: 'Paviliun Graha Amukti',
            tgl: new Date().toISOString().split('T')[0],
            penerima: 'Siti Aminah, A.Md.Kep',
            penerima_nip: '19880512 201201 2 004',
            penerima_jabatan: 'Kepala Ruangan Rawat Inap VIP',
            bast_nomor: '032 / 034 / 430.10.7 / 2026',
            volume: 1,
            satuan: 'Unit',
            kondisi: 'Baik',
            keterangan: 'Penempatan di Ruang VIP 01-04 Paviliun Graha Amukti'
        },

        init() {
            if (!this.isEdit) {
                this.formData = {
                    kode: 'DST-2026-' + String(Math.floor(Math.random() * 900) + 100),
                    nama: '',
                    kode_barang: '',
                    tujuan: '',
                    tgl: new Date().toISOString().split('T')[0],
                    penerima: '',
                    penerima_nip: '',
                    penerima_jabatan: '',
                    bast_nomor: '032 / 0' + String(Math.floor(Math.random() * 80) + 10) + ' / 430.10.7 / 2026',
                    volume: 1,
                    satuan: 'Unit',
                    kondisi: 'Baik',
                    keterangan: ''
                };
                this.barangSearch = '';
                this.selectedBarangObj = null;
                this.unitSearch = '';
                this.selectedUnitObj = null;
            } else {
                this.barangSearch = this.formData.nama;
                this.unitSearch = this.formData.tujuan;
                this.selectedUnitObj = this.unitList.find(u => u.nama === this.formData.tujuan) || null;
                if (this.selectedUnitObj) {
                    this.formData.penerima = this.selectedUnitObj.kepala;
                    this.formData.penerima_nip = this.selectedUnitObj.nip;
                    this.formData.penerima_jabatan = this.selectedUnitObj.jabatan;
                }
            }
        },

        // Filter Pencarian Barang ASTAP (Muncul Hanya Saat Diketik)
        get filteredBarangList() {
            if (!this.barangSearch || this.barangSearch.trim().length === 0) {
                return [];
            }
            const q = this.barangSearch.toLowerCase().trim();
            return this.katalogAstap.filter(item => 
                item.nama.toLowerCase().includes(q) || 
                item.kode.toLowerCase().includes(q) ||
                item.kategori.toLowerCase().includes(q) ||
                item.merk.toLowerCase().includes(q)
            );
        },

        // Aksi Pilih Barang dari Dropdown Hasil Pencarian
        selectBarang(item) {
            this.formData.nama = item.nama;
            this.formData.kode_barang = item.kode;
            this.formData.satuan = item.satuan || 'Unit';
            this.barangSearch = item.nama;
            this.selectedBarangObj = item;
            this.isSearchingBarang = false;
        },

        clearBarang() {
            this.formData.nama = '';
            this.formData.kode_barang = '';
            this.barangSearch = '';
            this.selectedBarangObj = null;
            this.isSearchingBarang = true;
        },

        // Filter Pencarian Unit / Paviliun (Muncul Hanya Saat Diketik)
        get filteredUnitList() {
            if (!this.unitSearch || this.unitSearch.trim().length === 0) {
                return [];
            }
            const q = this.unitSearch.toLowerCase().trim();
            return this.unitList.filter(u => 
                u.nama.toLowerCase().includes(q) || 
                u.tipe.toLowerCase().includes(q) ||
                u.kepala.toLowerCase().includes(q) ||
                u.jabatan.toLowerCase().includes(q)
            );
        },

        // Aksi Pilih Unit dari Dropdown Hasil Pencarian
        selectUnit(u) {
            this.formData.tujuan = u.nama;
            this.formData.penerima = u.kepala;
            this.formData.penerima_nip = u.nip;
            this.formData.penerima_jabatan = u.jabatan;
            this.unitSearch = u.nama;
            this.selectedUnitObj = u;
            this.isSearchingUnit = false;
        },

        clearUnit() {
            this.formData.tujuan = '';
            this.formData.penerima = '';
            this.formData.penerima_nip = '';
            this.formData.penerima_jabatan = '';
            this.unitSearch = '';
            this.selectedUnitObj = null;
            this.isSearchingUnit = true;
        },

        submitForm() {
            if (!this.formData.nama || this.formData.nama.trim() === '') {
                alert('⚠️ Silakan pilih atau ketik nama barang ASTAP yang akan didistribusikan!');
                return;
            }
            if (!this.formData.tujuan || this.formData.tujuan.trim() === '') {
                alert('⚠️ Silakan cari dan pilih Tujuan Unit / Paviliun penerima barang!');
                return;
            }
            alert('✅ Berhasil menyimpan distribusi barang:\n- Nama Barang: ' + this.formData.nama + '\n- Tujuan Unit: ' + this.formData.tujuan + '\n- Pegawai Penerima (Sub Admin): ' + this.formData.penerima + '\n- No. BAST: ' + this.formData.bast_nomor);
            window.location.href = '{{ route('distribusi.index') }}';
        }
    }" x-cloak class="space-y-6">

        <!-- Top Navigation Bar -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl">
            <div class="flex items-center space-x-4">
                <a href="{{ route('distribusi.index') }}" 
                   class="w-10 h-10 rounded-2xl bg-slate-950 border border-slate-800 hover:border-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <div class="inline-flex items-center space-x-2 px-2.5 py-0.5 rounded-full bg-teal-500/20 text-teal-300 border border-teal-500/30 text-[10px] font-bold mb-1">
                        <span x-text="isEdit ? '✏️ UBAH DISTRIBUSI BARANG' : '🚚 FORM DISTRIBUSI BARANG BARU'"></span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight" x-text="isEdit ? 'Ubah Distribusi: ' + (formData.nama || 'Barang') : 'Input Distribusi & Penyerahan ASTAP'"></h1>
                    <p class="text-xs text-slate-400 mt-0.5">Alokasi penyerahan barang aset rumah sakit ke unit kerja dan penanggung jawab ruangan (Sub Admin)</p>
                </div>
            </div>

            <div class="flex items-center space-x-3 w-full sm:w-auto justify-end">
                <a href="{{ route('distribusi.index') }}" 
                   class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-all">
                    Batal
                </a>
                <button type="button" @click="submitForm()"
                        class="px-5 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-teal-500/20 transition-all flex items-center space-x-1.5 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span x-text="isEdit ? 'Simpan Perubahan' : 'Simpan Distribusi Baru'"></span>
                </button>
            </div>
        </div>

        <!-- Form Card Utama -->
        <div class="mx-auto bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl max-w-full space-y-6">
            
            <!-- Baris 1: Nomor Registrasi & Nomor BAST Terkait -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5 flex items-center space-x-1.5">
                        <span>Nomor Registrasi Distribusi</span>
                        <span class="text-teal-400 font-normal text-[11px]">(Otomatis Sistem)</span>
                    </label>
                    <input type="text" x-model="formData.kode" readonly class="w-full bg-slate-950/80 border border-slate-800 rounded-xl px-4 py-3 text-xs text-teal-400 font-mono font-bold cursor-not-allowed select-none">
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5 flex items-center space-x-1.5">
                        <span>Nomor Berita Acara (BAST) Terkait</span>
                        <span class="text-purple-400 font-normal text-[11px]">(Format Resmi RSUD)</span>
                    </label>
                    <input type="text" x-model="formData.bast_nomor" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-purple-300 font-mono font-bold focus:outline-none focus:border-purple-500">
                </div>
            </div>

            <!-- Baris 2: NAMA BARANG ASTAP (FILTER AUTOCOMPLETE SEARCHABLE DROPDOWN) -->
            <div class="relative" @click.away="isSearchingBarang = false">
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-slate-200 font-bold text-xs flex items-center space-x-1.5">
                        <span>Nama Barang ASTAP yang Didistribusikan</span>
                        <span class="text-rose-400">*</span>
                    </label>
                    <span class="text-[11px] text-slate-400">Ketik huruf untuk memfilter katalog barang (misal: "em", "kasur", "pompa")</span>
                </div>

                <div class="relative">
                    <input type="text" 
                           x-model="barangSearch" 
                           @focus="isSearchingBarang = true"
                           @input="isSearchingBarang = true"
                           placeholder="Ketik nama barang untuk mencari (misal: 'em' untuk Ember / Emergency)..." 
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 pl-10 pr-10 text-xs text-white font-bold placeholder-slate-500 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all">
                    
                    <svg class="w-4 h-4 text-teal-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>

                    <!-- Tombol Bersihkan Pencarian Barang -->
                    <template x-if="barangSearch && barangSearch.length > 0">
                        <button type="button" @click="clearBarang()" class="absolute right-3.5 top-3 text-slate-400 hover:text-rose-400 transition-colors p-0.5 rounded">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </template>
                </div>

                <!-- DROPDOWN HASIL FILTER PENCARIAN BARANG (HANYA MUNCUL SAAT DIKETIK) -->
                <div x-show="isSearchingBarang && barangSearch.trim().length >= 1" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="absolute left-0 right-0 z-40 mt-1.5 bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl overflow-hidden max-h-72 overflow-y-auto divide-y divide-slate-800">
                    
                    <!-- Header Info Hasil Barang -->
                    <div class="px-4 py-2 bg-slate-950 text-[11px] font-bold text-slate-400 flex items-center justify-between">
                        <span>Hasil Pencarian Katalog ASTAP (<span x-text="filteredBarangList.length"></span> barang ditemukan)</span>
                        <span class="text-teal-400 text-[10px]">Klik salah satu item untuk memilih</span>
                    </div>

                    <template x-for="item in filteredBarangList" :key="item.kode">
                        <div @click="selectBarang(item)" class="px-4 py-3 hover:bg-teal-500/10 hover:border-teal-500/30 cursor-pointer transition-colors group">
                            <div class="flex items-start justify-between gap-3">
                                <div class="space-y-0.5 flex-1">
                                    <div class="font-bold text-white group-hover:text-teal-300 text-xs flex items-center space-x-2">
                                        <span class="text-teal-400">📦</span>
                                        <span x-text="item.nama"></span>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2 text-[10px] text-slate-400">
                                        <span class="font-mono text-cyan-400 font-semibold" x-text="item.kode"></span>
                                        <span>•</span>
                                        <span class="text-slate-300" x-text="item.kategori"></span>
                                        <span>•</span>
                                        <span class="text-slate-400" x-text="item.merk"></span>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 rounded-lg bg-teal-500/15 text-teal-300 border border-teal-500/30 text-[10px] font-bold shrink-0">
                                    Pilih &rarr;
                                </span>
                            </div>
                        </div>
                    </template>

                    <!-- Kondisi Jika Tidak Ada Barang yang Cocok -->
                    <template x-if="filteredBarangList.length === 0">
                        <div class="px-4 py-6 text-center text-xs text-slate-400 space-y-1">
                            <p class="font-bold text-amber-400">Tidak ada barang yang cocok dengan kata kunci "<span x-text="barangSearch"></span>"</p>
                            <p class="text-[11px] text-slate-500">Silakan periksa ejaan atau ketik nama umum barang lainnya.</p>
                        </div>
                    </template>
                </div>

                <!-- Detail Pill Barang yang Terpilih -->
                <template x-if="formData.nama && formData.nama.length > 0">
                    <div class="mt-2.5 p-3 rounded-2xl bg-teal-500/10 border border-teal-500/30 flex items-center justify-between gap-3">
                        <div class="flex items-center space-x-2.5">
                            <span class="p-2 rounded-xl bg-teal-500/20 text-teal-300 text-sm">✅</span>
                            <div>
                                <div class="text-xs font-extrabold text-teal-200" x-text="formData.nama"></div>
                                <div class="text-[10px] font-mono text-cyan-300" x-text="'Kode 108: ' + (formData.kode_barang || 'Katalog ASTAP Terdaftar')"></div>
                            </div>
                        </div>
                        <button type="button" @click="clearBarang()" class="px-3 py-1 rounded-xl bg-rose-500/10 text-rose-300 hover:bg-rose-500/20 border border-rose-500/30 font-bold text-[10px] transition-all">
                            Ganti Barang
                        </button>
                    </div>
                </template>
            </div>

            <!-- Baris 3: TUJUAN UNIT / PAVILIUN (FILTER AUTOCOMPLETE DIKETIK) & TANGGAL DISTRIBUSI -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <!-- FILTER AUTOCOMPLETE PENCARIAN UNIT / PAVILIUN -->
                <div class="relative" @click.away="isSearchingUnit = false">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-slate-300 font-semibold text-xs flex items-center space-x-1.5">
                            <span>Tujuan Unit / Paviliun (Ruangan Penerima)</span>
                            <span class="text-rose-400">*</span>
                        </label>
                        <span class="text-[10px] text-teal-400">Ketik nama unit</span>
                    </div>

                    <div class="relative">
                        <input type="text" 
                               x-model="unitSearch" 
                               @focus="isSearchingUnit = true"
                               @input="isSearchingUnit = true"
                               placeholder="Ketik untuk mencari unit (misal: 'graha', 'igd', 'radiologi', 'icu')..." 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 pl-10 pr-10 text-xs text-white font-bold placeholder-slate-500 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all">
                        
                        <svg class="w-4 h-4 text-teal-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>

                        <!-- Tombol Bersihkan Pencarian Unit -->
                        <template x-if="unitSearch && unitSearch.length > 0">
                            <button type="button" @click="clearUnit()" class="absolute right-3.5 top-3 text-slate-400 hover:text-rose-400 transition-colors p-0.5 rounded">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </template>
                    </div>

                    <!-- DROPDOWN HASIL FILTER PENCARIAN UNIT (HANYA MUNCUL SAAT DIKETIK) -->
                    <div x-show="isSearchingUnit && unitSearch.trim().length >= 1" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute left-0 right-0 z-40 mt-1.5 bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl overflow-hidden max-h-64 overflow-y-auto divide-y divide-slate-800">
                        
                        <!-- Header Info Hasil Unit -->
                        <div class="px-4 py-2 bg-slate-950 text-[11px] font-bold text-slate-400 flex items-center justify-between">
                            <span>Daftar Unit & Paviliun RSUD (<span x-text="filteredUnitList.length"></span> ditemukan)</span>
                            <span class="text-teal-400 text-[10px]">Klik untuk memilih unit</span>
                        </div>

                        <template x-for="u in filteredUnitList" :key="u.id">
                            <div @click="selectUnit(u)" class="px-4 py-3 hover:bg-teal-500/10 hover:border-teal-500/30 cursor-pointer transition-colors group">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="space-y-0.5 flex-1">
                                        <div class="font-bold text-white group-hover:text-teal-300 text-xs flex items-center space-x-2">
                                            <span class="text-teal-400">🏥</span>
                                            <span x-text="u.nama"></span>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-2 text-[10px] text-slate-400">
                                            <span class="text-cyan-300 font-semibold" x-text="u.tipe"></span>
                                            <span>•</span>
                                            <span class="text-slate-300" x-text="'PJ: ' + u.kepala"></span>
                                        </div>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-lg bg-teal-500/15 text-teal-300 border border-teal-500/30 text-[10px] font-bold shrink-0">
                                        Pilih &rarr;
                                    </span>
                                </div>
                            </div>
                        </template>

                        <!-- Kondisi Jika Tidak Ada Unit yang Cocok -->
                        <template x-if="filteredUnitList.length === 0">
                            <div class="px-4 py-6 text-center text-xs text-slate-400 space-y-1">
                                <p class="font-bold text-amber-400">Tidak ada unit yang cocok dengan kata kunci "<span x-text="unitSearch"></span>"</p>
                                <p class="text-[11px] text-slate-500">Ketik kata kunci unit lain (misal: 'graha', 'igd', 'radiologi', 'bedah', 'icu').</p>
                            </div>
                        </template>
                    </div>

                    <!-- Detail Pill Unit yang Terpilih -->
                    <template x-if="formData.tujuan && formData.tujuan.length > 0">
                        <div class="mt-2.5 p-2.5 rounded-xl bg-teal-500/10 border border-teal-500/30 flex items-center justify-between gap-2">
                            <div class="flex items-center space-x-2">
                                <span class="text-sm">🏥</span>
                                <div class="text-xs font-bold text-teal-200" x-text="formData.tujuan"></div>
                            </div>
                            <button type="button" @click="clearUnit()" class="px-2.5 py-0.5 rounded-lg bg-rose-500/10 text-rose-300 hover:bg-rose-500/20 border border-rose-500/30 font-bold text-[10px] transition-all">
                                Ganti Unit
                            </button>
                        </div>
                    </template>
                </div>

                <!-- TANGGAL PELAKSANAAN DISTRIBUSI -->
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Tanggal Pelaksanaan Distribusi</label>
                    <input type="date" x-model="formData.tgl" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white font-bold focus:outline-none focus:border-teal-500">
                </div>
            </div>

            <!-- Baris 4: NAMA PEGAWAI PENERIMA (OTOMATIS TERHUBUNG & TERKUNCI/READONLY) -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-slate-300 font-semibold text-xs flex items-center space-x-2">
                        <span>Nama Pegawai Penerima (Penanggung Jawab Unit / Sub Admin)</span>
                        <span class="px-2 py-0.5 rounded-md bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 text-[10px] font-bold">
                            🔒 Terhubung Otomatis & Terkunci
                        </span>
                    </label>
                </div>
                
                <!-- Input Terkunci / Read-Only -->
                <div class="relative">
                    <input type="text" 
                           x-model="formData.penerima" 
                           placeholder="Otomatis terisi saat unit tujuan dipilih..."
                           readonly 
                           class="w-full bg-slate-950/80 border border-slate-800 rounded-xl px-4 py-3 pl-10 text-xs text-emerald-400 font-bold placeholder-slate-600 cursor-not-allowed select-none shadow-inner">
                    <svg class="w-4 h-4 text-emerald-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>

                <!-- Info Box Pegawai Penerima -->
                <div class="mt-2 p-3 rounded-2xl bg-slate-950 border border-slate-800/80 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                    <div class="flex items-center space-x-2">
                        <span class="text-slate-400">👤 NIP:</span>
                        <span class="font-mono font-bold text-slate-200" x-text="formData.penerima_nip || '-'"></span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-slate-400">📋 Jabatan:</span>
                        <span class="font-semibold text-teal-300" x-text="formData.penerima_jabatan || 'Kepala Ruangan / Penanggung Jawab Unit'"></span>
                    </div>
                </div>
            </div>

            <!-- Baris 5: Volume Kuantitas & Kondisi Barang -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Jumlah Volume Barang</label>
                    <input type="number" min="1" x-model="formData.volume" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white font-bold focus:outline-none focus:border-teal-500">
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Satuan</label>
                    <select x-model="formData.satuan" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white font-bold focus:outline-none focus:border-teal-500">
                        <option value="Unit">Unit</option>
                        <option value="Set">Set</option>
                        <option value="Pcs">Pcs</option>
                        <option value="Buah">Buah</option>
                        <option value="Paket">Paket</option>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Kondisi Barang Saat Diserahkan</label>
                    <select x-model="formData.kondisi" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white font-bold focus:outline-none focus:border-teal-500">
                        <option value="Baik">Baik (100% Layak Pakai)</option>
                        <option value="Kurang Baik">Kurang Baik</option>
                    </select>
                </div>
            </div>

            <!-- Baris 6: Catatan / Keterangan Penempatan -->
            <div>
                <label class="block text-slate-300 font-semibold text-xs mb-1.5">Catatan / Keterangan Penempatan Ruangan</label>
                <textarea x-model="formData.keterangan" rows="3" placeholder="Contoh: Ditempatkan di Ruang Perawatan VIP 01-04 atau Ruang Tindakan Bedah..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-teal-500"></textarea>
            </div>

            <!-- Tombol Aksi Simpan -->
            <div class="pt-6 border-t border-slate-800 flex items-center justify-end space-x-3">
                <a href="{{ route('distribusi.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-all">
                    Batal
                </a>
                <button type="button" @click="submitForm()" class="px-6 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-teal-500/20 transition-all flex items-center space-x-1.5 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span x-text="isEdit ? 'Simpan Perubahan' : 'Simpan Distribusi Baru'"></span>
                </button>
            </div>
        </div>

    </div>
</x-layout>
