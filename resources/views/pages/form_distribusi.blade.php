<x-layout :title="request()->routeIs('distribusi.edit') ? 'Ubah Distribusi ASTAP - SIMAT-RK' : 'Input Distribusi Baru - SIMAT-RK'">
    @section('page-title', request()->routeIs('distribusi.edit') ? 'Ubah Distribusi ASTAP' : 'Input Distribusi Baru')
    @section('breadcrumb', request()->routeIs('distribusi.edit') ? 'Master Utama / Distribusi ASTAP / Ubah' : 'Master Utama / Distribusi ASTAP / Input Baru')

    <div x-data="{
        isEdit: {{ request()->routeIs('distribusi.edit') ? 'true' : 'false' }},
        
        // Autocomplete Search Unit / Paviliun State
        unitSearch: '',
        isSearchingUnit: false,
        selectedUnitObj: null,

        // Quick Barang Picker Modal State
        showBarangPicker: false,
        activeItemIndex: null,
        barangPickerQuery: '',

        // Database Master Katalog Barang ASTAP RSUD
        katalogAstap: [
            { kode: '1.3.2.05.01.04.008', nama: 'Kasur Matras spoon (Mattress Foam Adult 200x90x10)', kategori: 'Perlengkapan Kamar Pasien', merk: 'Mattres Cover Spon FO R.Inap', satuan: 'Unit' },
            { kode: '1.3.2.02.01.08.001', nama: 'Bed Patient Electric 3 Crank Acare', kategori: 'Perlengkapan Kamar Rawat Inap', merk: 'Acare Electric Medical Bed with Side Rail', satuan: 'Unit' },
            { kode: '1.3.2.02.01.08.002', nama: 'Bed Patient Manual 2 Crank Paramount', kategori: 'Perlengkapan Kamar Rawat Inap', merk: 'Paramount Bed Standard with Side Rail', satuan: 'Unit' },
            { kode: '1.3.2.02.01.01.025', nama: 'Emergency Crash Cart Trolley Kit Lengkap', kategori: 'Alat Kedokteran Gawat Darurat', merk: 'Paramount Emergency 5 Laci + Tiang Infus', satuan: 'Unit' },
            { kode: '1.3.2.02.01.01.008', nama: 'Patient Monitor 6 Parameter Mindray', kategori: 'Alat Monitoring Medis', merk: 'Mindray ePM 12 / Display 12.1 Inch Multi-Lead', satuan: 'Unit' },
            { kode: '1.3.2.02.01.01.012', nama: 'Infusion Pump Digital Otomatis Terumo', kategori: 'Alat Kedokteran Tindakan Medis', merk: 'Terumo TE-LM700 / TE-112', satuan: 'Unit' },
            { kode: '1.3.2.02.01.01.015', nama: 'Syringe Pump Terumo TE-331', kategori: 'Alat Kedokteran Tindakan Medis', merk: 'Terumo TE-331 Digital Infusion System', satuan: 'Unit' },
            { kode: '1.3.2.02.01.02.007', nama: 'Suction Pump Portable Medis Thomas', kategori: 'Alat Penghisap Lendir Medis', merk: 'Thomas 1632 Aspirator Heavy Duty', satuan: 'Unit' },
            { kode: '1.3.2.01.03.05.005', nama: 'Submersible Pump 7.5 HP Franklin Electric', kategori: 'Peralatan Mesin & Sanitasi', merk: 'Franklin Electric 4 Inch 3-Phase', satuan: 'Unit' },
            { kode: '1.3.2.01.03.05.012', nama: 'Ball Valve Kuningan Heavy Duty 3 Inch', kategori: 'Peralatan Perpipaan & Sarpras', merk: 'Kitz Heavy Duty Brass 10K', satuan: 'Pcs' },
            { kode: '1.3.2.10.01.02.003', nama: 'Laptop Operasional ASUS ExpertBook Core i7', kategori: 'Peralatan Komputer & IT', merk: 'ASUS ExpertBook B1402CBA / 16GB / 512GB SSD', satuan: 'Unit' },
            { kode: '1.3.2.10.02.01.005', nama: 'Printer Thermal Resep & Label Rekam Medis', kategori: 'Peralatan IT & Farmasi', merk: 'Epson TM-T82X Thermal Auto-Cutter USB', satuan: 'Unit' },
            { kode: '1.3.2.05.01.01.012', nama: 'Kursi Tunggu Stainless 4 Dudukan Ruang Poli', kategori: 'Mebelair & Sarana Pasien', merk: 'Indachi Stainless Steel 4-Seater', satuan: 'Unit' },
            { kode: '1.3.2.05.01.02.006', nama: 'Lemari Obat Kaca 2 Pintu Farmasi Rawat Inap', kategori: 'Mebelair Medis & Farmasi', merk: 'Baja Coating Glass Door Cabinet', satuan: 'Unit' },
            { kode: '1.3.2.02.01.04.005', nama: 'Meja Tindakan Stainless Steel IGD', kategori: 'Alat Medis & Tindakan', merk: 'Stainless 304 Examination Table', satuan: 'Unit' },
            { kode: '1.3.2.02.01.01.018', nama: 'Tensimeter Digital Stand Mobile Riester', kategori: 'Alat Diagnostik & TTV', merk: 'Riester Ri-Champion Mobile Stand', satuan: 'Unit' },
            { kode: '1.3.2.02.01.06.004', nama: 'Tabung Oksigen Medis 6m3 + Regulator Flowmeter', kategori: 'Gas Medis & Resusitasi', merk: 'Samator Medical Gas Cylinder 6m3', satuan: 'Unit' },
            { kode: '1.3.2.05.02.01.004', nama: 'Ember Plastik Tertutup Medis / Non-Medis (50 Liter)', kategori: 'Peralatan Sanitasi & Kebersihan', merk: 'Clio Plastik / Lion Star 50L', satuan: 'Unit' }
        ],

        // Database Master Unit & Pegawai Penerima (Dinamis dari Tabel Units)
        unitList: {{ Js::from($units ?? []) }},

        // Data Form Distribusi dengan Dukungan Multi-Barang
        formData: {
            kode: 'DST-2026-004',
            bast_nomor: '032 / 034 / 430.10.7 / 2026',
            tujuan: 'IGD',
            tgl: new Date().toISOString().split('T')[0],
            penerima: 'dr. ADHI SUDARMADJI',
            penerima_nip: '198410272009021003',
            penerima_jabatan: 'Kepala IGD',
            keterangan: 'Distribusi alokasi sarana prasarana dan alat penunjang medis ruangan',
            
            // Daftar Barang yang Didistribusikan Sekaligus (Multi-Barang)
            items: [
                {
                    id: 1,
                    nama_barang: 'Patient Monitor 6 Parameter Mindray',
                    kode_barang: '1.3.2.02.01.01.008',
                    merk_type: 'Mindray ePM 12 / Display 12.1 Inch Multi-Lead',
                    qty: 4,
                    satuan: 'Unit',
                    kondisi: 'Baik',
                    keterangan: 'Zona Kritis Resusitasi IGD'
                },
                {
                    id: 2,
                    nama_barang: 'Emergency Crash Cart Trolley Kit Lengkap',
                    kode_barang: '1.3.2.02.01.01.025',
                    merk_type: 'Paramount Emergency 5 Laci + Tiang Infus',
                    qty: 2,
                    satuan: 'Unit',
                    kondisi: 'Baik',
                    keterangan: 'Peralatan Siaga Resusitasi IGD'
                }
            ]
        },

        init() {
            if (!this.isEdit) {
                this.formData = {
                    kode: 'DST-2026-' + String(Math.floor(Math.random() * 900) + 100),
                    bast_nomor: '032 / 0' + String(Math.floor(Math.random() * 80) + 10) + ' / 430.10.7 / 2026',
                    tujuan: '',
                    tgl: new Date().toISOString().split('T')[0],
                    penerima: '',
                    penerima_nip: '',
                    penerima_jabatan: '',
                    keterangan: '',
                    items: [
                        {
                            id: Date.now(),
                            nama_barang: '',
                            kode_barang: '',
                            merk_type: '',
                            qty: 1,
                            satuan: 'Unit',
                            kondisi: 'Baik',
                            keterangan: ''
                        }
                    ]
                };
                this.unitSearch = '';
                this.selectedUnitObj = null;
            } else {
                this.unitSearch = this.formData.tujuan;
                this.selectedUnitObj = this.unitList.find(u => u.nama === this.formData.tujuan) || null;
                if (this.selectedUnitObj) {
                    this.formData.penerima = this.selectedUnitObj.kepala;
                    this.formData.penerima_nip = this.selectedUnitObj.nip;
                    this.formData.penerima_jabatan = this.selectedUnitObj.jabatan;
                }
            }
        },

        // Tambah Baris Barang Baru dalam Satu Distribusi
        addItem() {
            this.formData.items.push({
                id: Date.now() + Math.random(),
                nama_barang: '',
                kode_barang: '',
                merk_type: '',
                qty: 1,
                satuan: 'Unit',
                kondisi: 'Baik',
                keterangan: ''
            });
        },

        // Hapus Baris Barang
        removeItem(index) {
            if (this.formData.items.length <= 1) {
                alert('⚠️ Minimal harus ada 1 barang dalam transaksi distribusi!');
                return;
            }
            this.formData.items.splice(index, 1);
        },

        // Buka Modal Picker Katalog Barang
        openBarangPicker(index) {
            this.activeItemIndex = index;
            this.barangPickerQuery = '';
            this.showBarangPicker = true;
        },

        // Pilih Barang dari Katalog
        selectBarangForActiveItem(katalogItem) {
            if (this.activeItemIndex !== null && this.formData.items[this.activeItemIndex]) {
                this.formData.items[this.activeItemIndex].nama_barang = katalogItem.nama;
                this.formData.items[this.activeItemIndex].kode_barang = katalogItem.kode;
                this.formData.items[this.activeItemIndex].merk_type = katalogItem.merk;
                this.formData.items[this.activeItemIndex].satuan = katalogItem.satuan || 'Unit';
            }
            this.showBarangPicker = false;
            this.activeItemIndex = null;
        },

        get filteredPickerKatalog() {
            if (!this.barangPickerQuery || this.barangPickerQuery.trim().length === 0) {
                return this.katalogAstap;
            }
            const q = this.barangPickerQuery.toLowerCase().trim();
            return this.katalogAstap.filter(item => 
                item.nama.toLowerCase().includes(q) || 
                item.kode.toLowerCase().includes(q) ||
                item.kategori.toLowerCase().includes(q) ||
                item.merk.toLowerCase().includes(q)
            );
        },

        // Filter Pencarian Unit / Paviliun
        get filteredUnitList() {
            if (!this.unitSearch || this.unitSearch.trim().length === 0) {
                return this.unitList.slice(0, 10);
            }
            const q = this.unitSearch.toLowerCase().trim();
            return this.unitList.filter(u => 
                u.nama.toLowerCase().includes(q) || 
                (u.kode && u.kode.toLowerCase().includes(q)) ||
                (u.tipe && u.tipe.toLowerCase().includes(q)) ||
                (u.kepala && u.kepala.toLowerCase().includes(q))
            );
        },

        // Aksi Pilih Unit
        selectUnit(u) {
            this.formData.tujuan = u.nama;
            this.formData.penerima = u.kepala;
            this.formData.penerima_nip = u.nip;
            this.formData.penerima_jabatan = u.jabatan || ('Kepala Ruangan ' + u.nama);
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

        getTotalItemVolume() {
            return this.formData.items.reduce((acc, curr) => acc + (parseInt(curr.qty) || 0), 0);
        },

        submitForm() {
            if (!this.formData.tujuan || this.formData.tujuan.trim() === '') {
                alert('⚠️ Silakan pilih Tujuan Unit / Paviliun penerima barang!');
                return;
            }

            const emptyItem = this.formData.items.find(it => !it.nama_barang || it.nama_barang.trim() === '');
            if (emptyItem) {
                alert('⚠️ Ada baris barang yang belum diisi nama barangnya. Silakan lengkapi atau hapus baris yang kosong!');
                return;
            }

            alert('✅ Berhasil menyimpan distribusi barang:\n- No. Distribusi: ' + this.formData.kode + '\n- Tujuan Unit: ' + this.formData.tujuan + '\n- Penerima: ' + this.formData.penerima + '\n- Jumlah Barang: ' + this.formData.items.length + ' Jenis Barang (' + this.getTotalItemVolume() + ' Total Volume)');
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
                        <span x-text="isEdit ? '✏️ UBAH DISTRIBUSI BARANG' : '🚚 INPUT DISTRIBUSI MULTI-BARANG'"></span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight">Form Distribusi & Penyerahan ASTAP</h1>
                    <p class="text-xs text-slate-400 mt-0.5">Dapat memasukkan beberapa barang berbeda sekaligus dalam satu transaksi penyerahan ke ruangan</p>
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
            
            <!-- BAGIAN 1: INFORMASI DOKUMEN & RUANGAN TUJUAN -->
            <div>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4 flex items-center space-x-2 border-b border-slate-800 pb-2">
                    <span class="w-2 h-2 rounded-full bg-teal-400"></span>
                    <span>1. Informasi Penyerahan & Ruangan Tujuan</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                    <!-- Nomor Registrasi Distribusi -->
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5 flex items-center space-x-1.5">
                            <span>Nomor Registrasi Distribusi</span>
                            <span class="text-teal-400 font-normal text-[11px]">(Otomatis)</span>
                        </label>
                        <input type="text" x-model="formData.kode" readonly class="w-full bg-slate-950/80 border border-slate-800 rounded-xl px-4 py-3 text-xs text-teal-400 font-mono font-bold cursor-not-allowed select-none">
                    </div>

                    <!-- Nomor BAST -->
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Nomor Berita Acara (BAST)</label>
                        <input type="text" x-model="formData.bast_nomor" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-purple-300 font-mono font-bold focus:outline-none focus:border-purple-500">
                    </div>

                    <!-- Tanggal Distribusi -->
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Tanggal Pelaksanaan Distribusi</label>
                        <input type="date" x-model="formData.tgl" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white font-bold focus:outline-none focus:border-teal-500">
                    </div>
                </div>

                <!-- TUJUAN UNIT / PAVILIUN & PEGAWAI PENERIMA OTOMATIS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Autocomplete Unit -->
                    <div class="relative" @click.away="isSearchingUnit = false">
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5 flex items-center justify-between">
                            <span>Tujuan Unit / Ruangan Penerima</span>
                            <span class="text-[10px] text-teal-400">Pilih dari 55+ Unit RSUD</span>
                        </label>
                        
                        <div class="relative">
                            <input type="text" 
                                   x-model="unitSearch" 
                                   @focus="isSearchingUnit = true"
                                   @input="isSearchingUnit = true"
                                   placeholder="Ketik untuk mencari ruangan (misal: IGD, Melati, Radiologi, Bedah)..." 
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 pl-10 pr-10 text-xs text-white font-bold placeholder-slate-500 focus:outline-none focus:border-teal-500 transition-all">
                            
                            <svg class="w-4 h-4 text-teal-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>

                            <template x-if="unitSearch && unitSearch.length > 0">
                                <button type="button" @click="clearUnit()" class="absolute right-3.5 top-3 text-slate-400 hover:text-rose-400 p-0.5 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </template>
                        </div>

                        <!-- Dropdown Unit List -->
                        <div x-show="isSearchingUnit" 
                             class="absolute left-0 right-0 z-40 mt-1 bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl overflow-hidden max-h-64 overflow-y-auto divide-y divide-slate-800">
                            <template x-for="u in filteredUnitList" :key="u.id">
                                <div @click="selectUnit(u)" class="px-4 py-2.5 hover:bg-teal-500/10 cursor-pointer transition-colors group">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="font-bold text-white text-xs group-hover:text-teal-300 flex items-center space-x-1.5">
                                                <span>🏥</span>
                                                <span x-text="u.nama"></span>
                                            </div>
                                            <div class="text-[10px] text-slate-400" x-text="(u.kode || '') + ' • ' + (u.tipe || '') + ' • PJ: ' + u.kepala"></div>
                                        </div>
                                        <span class="text-[10px] text-teal-400 font-bold px-2 py-0.5 rounded bg-teal-500/10">Pilih</span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Pegawai Penerima Otomatis Terhubung -->
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5 flex items-center justify-between">
                            <span>Pegawai Penerima (Penanggung Jawab / Sub Admin)</span>
                            <span class="text-[10px] text-emerald-400">🔒 Otomatis Terhubung</span>
                        </label>
                        <input type="text" x-model="formData.penerima" readonly placeholder="Pilih unit tujuan untuk mengisi otomatis..."
                               class="w-full bg-slate-950/80 border border-slate-800 rounded-xl px-4 py-3 text-xs text-emerald-300 font-bold cursor-not-allowed select-none shadow-inner">
                        <p class="text-[11px] text-slate-400 mt-1" x-show="formData.penerima_nip">
                            NIP: <span class="text-white font-mono" x-text="formData.penerima_nip"></span> · Jabatan: <span class="text-teal-300 font-semibold" x-text="formData.penerima_jabatan"></span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- BAGIAN 2: RINCIAN DAFTAR BARANG (MULTI-BARANG DALAM SEKALI DISTRIBUSI) -->
            <div class="pt-4 border-t border-slate-800">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center space-x-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            <span>2. Rincian Barang yang Didistribusikan (Bisa Beberapa Barang)</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Tambahkan satu atau beberapa item barang aset berbeda yang diserahkan dalam transaksi ini</p>
                    </div>

                    <button type="button" @click="addItem()"
                            class="px-4 py-2 rounded-xl bg-teal-500/20 hover:bg-teal-500/30 text-teal-300 border border-teal-500/40 text-xs font-bold transition-all flex items-center space-x-1.5 self-start sm:self-auto shadow-sm active:scale-95">
                        <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>+ Tambah Barang Lagi</span>
                    </button>
                </div>

                <!-- Table Form Input Multi-Barang -->
                <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-950/60 p-1">
                    <table class="w-full text-left text-xs text-slate-300 min-w-[700px]">
                        <thead class="bg-slate-950 text-slate-400 font-bold uppercase tracking-wider text-[10px] border-b border-slate-800">
                            <tr>
                                <th class="px-3 py-3 text-center w-10">No</th>
                                <th class="px-3 py-3 min-w-[240px]">Nama Barang & Kode 108</th>
                                <th class="px-3 py-3 min-w-[180px]">Merk / Tipe / Spesifikasi</th>
                                <th class="px-3 py-3 w-24 text-center">Vol (Qty)</th>
                                <th class="px-3 py-3 w-28 text-center">Satuan</th>
                                <th class="px-3 py-3 w-32 text-center">Kondisi</th>
                                <th class="px-3 py-3 min-w-[150px]">Keterangan</th>
                                <th class="px-3 py-3 text-center w-14">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80">
                            <template x-for="(item, idx) in formData.items" :key="item.id">
                                <tr class="hover:bg-slate-900/50 transition-colors">
                                    <td class="px-3 py-3 text-center font-bold text-slate-500" x-text="idx + 1"></td>
                                    
                                    <!-- Nama Barang & Tombol Pilih dari Katalog -->
                                    <td class="px-3 py-3">
                                        <div class="space-y-1">
                                            <div class="flex items-center space-x-1.5">
                                                <input type="text" x-model="item.nama_barang" placeholder="Ketik / pilih dari katalog..."
                                                       class="w-full bg-slate-900 border border-slate-800 rounded-lg px-2.5 py-1.5 text-xs text-white font-bold focus:outline-none focus:border-teal-500">
                                                <button type="button" @click="openBarangPicker(idx)"
                                                        title="Pilih dari Master Katalog ASTAP"
                                                        class="p-1.5 rounded-lg bg-teal-500/15 hover:bg-teal-500/25 text-teal-300 border border-teal-500/30 text-xs shrink-0 transition-all">
                                                    🔍
                                                </button>
                                            </div>
                                            <div class="flex items-center space-x-2 text-[10px]">
                                                <span class="text-slate-400">Kode:</span>
                                                <input type="text" x-model="item.kode_barang" placeholder="1.3.2..."
                                                       class="bg-slate-900 border border-slate-800 rounded px-1.5 py-0.5 text-[10px] text-cyan-300 font-mono focus:outline-none w-36">
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Merk / Tipe -->
                                    <td class="px-3 py-3">
                                        <input type="text" x-model="item.merk_type" placeholder="Merk / Tipe barang..."
                                               class="w-full bg-slate-900 border border-slate-800 rounded-lg px-2.5 py-1.5 text-xs text-slate-200 focus:outline-none focus:border-teal-500">
                                    </td>

                                    <!-- Kuantitas (Volume) -->
                                    <td class="px-3 py-3 text-center">
                                        <input type="number" min="1" x-model="item.qty" 
                                               class="w-20 bg-slate-900 border border-slate-800 rounded-lg px-2 py-1.5 text-xs text-center text-white font-bold focus:outline-none focus:border-teal-500 font-mono">
                                    </td>

                                    <!-- Satuan -->
                                    <td class="px-3 py-3 text-center">
                                        <select x-model="item.satuan" class="w-full bg-slate-900 border border-slate-800 rounded-lg px-2 py-1.5 text-xs text-white focus:outline-none focus:border-teal-500">
                                            <option value="Unit">Unit</option>
                                            <option value="Set">Set</option>
                                            <option value="Pcs">Pcs</option>
                                            <option value="Buah">Buah</option>
                                            <option value="Paket">Paket</option>
                                        </select>
                                    </td>

                                    <!-- Kondisi -->
                                    <td class="px-3 py-3 text-center">
                                        <select x-model="item.kondisi" class="w-full bg-slate-900 border border-slate-800 rounded-lg px-2 py-1.5 text-xs text-emerald-400 font-bold focus:outline-none focus:border-teal-500">
                                            <option value="Baik">Baik</option>
                                            <option value="Kurang Baik">Kurang Baik</option>
                                        </select>
                                    </td>

                                    <!-- Keterangan Item -->
                                    <td class="px-3 py-3">
                                        <input type="text" x-model="item.keterangan" placeholder="Catatan per item..."
                                               class="w-full bg-slate-900 border border-slate-800 rounded-lg px-2.5 py-1.5 text-xs text-slate-300 focus:outline-none focus:border-teal-500">
                                    </td>

                                    <!-- Tombol Hapus Baris -->
                                    <td class="px-3 py-3 text-center">
                                        <button type="button" @click="removeItem(idx)"
                                                title="Hapus baris barang ini"
                                                class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/30 transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Ringkasan Volume Multi-Barang -->
                <div class="mt-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2 p-3 bg-slate-950 rounded-2xl border border-slate-800 text-xs">
                    <div class="flex items-center space-x-3 text-slate-300">
                        <span>Total: <strong class="text-teal-400" x-text="formData.items.length + ' Jenis Barang'"></strong></span>
                        <span>•</span>
                        <span>Akumulasi Volume: <strong class="text-emerald-400" x-text="getTotalItemVolume() + ' Total Item/Unit'"></strong></span>
                    </div>

                    <button type="button" @click="addItem()" class="text-teal-400 hover:text-teal-300 font-bold flex items-center space-x-1">
                        <span>+ Tambah Baris Barang</span>
                    </button>
                </div>
            </div>

            <!-- BAGIAN 3: CATATAN UMUM PENEMPATAN -->
            <div class="pt-4 border-t border-slate-800">
                <label class="block text-slate-300 font-semibold text-xs mb-1.5">Catatan Umum / Keterangan Penempatan</label>
                <textarea x-model="formData.keterangan" rows="2" placeholder="Contoh: Pengadaan DAK Kesehatan / BLUD untuk kelengkapan ruangan..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-teal-500"></textarea>
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

        <!-- MODAL PICKER MASTER KATALOG ASTAP -->
        <div x-show="showBarangPicker" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
            <div @click.away="showBarangPicker = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-2xl w-full p-6 shadow-2xl space-y-4 max-h-[85vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div>
                        <h3 class="text-base font-extrabold text-white">📦 Pilih dari Master Katalog ASTAP</h3>
                        <p class="text-xs text-slate-400">Pilih barang dari standar inventaris RSUD untuk baris terpilih</p>
                    </div>
                    <button type="button" @click="showBarangPicker = false" class="text-slate-400 hover:text-white text-lg font-bold">&times;</button>
                </div>

                <!-- Search Input in Picker -->
                <div class="relative">
                    <input type="text" x-model="barangPickerQuery" placeholder="Cari nama barang / kode 108 / merk..."
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 pl-10 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-teal-500">
                    <svg class="w-4 h-4 text-teal-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <!-- List Items in Picker -->
                <div class="space-y-1.5 max-h-96 overflow-y-auto divide-y divide-slate-800/80">
                    <template x-for="ast in filteredPickerKatalog" :key="ast.kode">
                        <div @click="selectBarangForActiveItem(ast)"
                             class="p-3 rounded-xl hover:bg-teal-500/10 border border-transparent hover:border-teal-500/30 cursor-pointer transition-all flex items-start justify-between gap-3 group">
                            <div>
                                <p class="font-bold text-white text-xs group-hover:text-teal-300" x-text="ast.nama"></p>
                                <p class="text-[10px] text-cyan-400 font-mono" x-text="ast.kode + ' • ' + ast.kategori"></p>
                                <p class="text-[11px] text-slate-400" x-text="'Merk: ' + ast.merk"></p>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg bg-teal-500/20 text-teal-300 text-[10px] font-bold shrink-0">
                                Pilih &rarr;
                            </span>
                        </div>
                    </template>
                </div>

                <div class="pt-3 border-t border-slate-800 flex justify-end">
                    <button type="button" @click="showBarangPicker = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs font-semibold">Tutup</button>
                </div>
            </div>
        </div>

    </div>
</x-layout>
