<x-layout :title="request()->routeIs('distribusi.edit') ? 'Ubah Distribusi ASTAP - SIMAT-RK' : 'Input Distribusi Baru - SIMAT-RK'">
    @section('page-title', request()->routeIs('distribusi.edit') ? 'Ubah Distribusi ASTAP' : 'Input Distribusi Baru')
    @section('breadcrumb', request()->routeIs('distribusi.edit') ? 'Master Utama / Distribusi ASTAP / Ubah' : 'Master Utama / Distribusi ASTAP / Input Baru')

    <div x-data="{
        isEdit: {{ request()->routeIs('distribusi.edit') ? 'true' : 'false' }},
        
        // Autocomplete Search Unit / Paviliun State
        unitSearch: '',
        isSearchingUnit: false,
        selectedUnitObj: null,

        // Active Autocomplete Dropdown Index for Items
        activeDropdownIndex: null,

        // Database Master Katalog Barang ASTAP RSUD (Lengkap dengan Satuan Resminya)
        katalogAstap: [
            { kode: '1.3.2.05.01.04.008', nama: 'Kasur Matras spoon (Mattress Foam Adult 200x90x10)', kategori: 'Perlengkapan Kamar Pasien', merk: 'Mattres Cover Spon FO R.Inap', satuan: 'Unit' },
            { kode: '1.3.2.02.01.08.001', nama: 'Bed Patient Electric 3 Crank Acare', kategori: 'Perlengkapan Kamar Rawat Inap', merk: 'Acare Electric Medical Bed with Side Rail', satuan: 'Unit' },
            { kode: '1.3.2.02.01.08.002', nama: 'Bed Patient Manual 2 Crank Paramount', kategori: 'Perlengkapan Kamar Rawat Inap', merk: 'Paramount Bed Standard with Side Rail', satuan: 'Unit' },
            { kode: '1.3.2.02.01.01.025', nama: 'Emergency Crash Cart Trolley Kit Lengkap', kategori: 'Alat Kedokteran Gawat Darurat', merk: 'Paramount Emergency 5 Laci + Tiang Infus', satuan: 'Set' },
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
            { kode: '1.3.2.02.01.01.018', nama: 'Tensimeter Digital Stand Mobile Riester', kategori: 'Alat Diagnostik & TTV', merk: 'Riester Ri-Champion Mobile Stand', satuan: 'Set' },
            { kode: '1.3.2.02.01.06.004', nama: 'Tabung Oksigen Medis 6m3 + Regulator Flowmeter', kategori: 'Gas Medis & Resusitasi', merk: 'Samator Medical Gas Cylinder 6m3', satuan: 'Tabung' },
            { kode: '1.3.2.05.02.01.004', nama: 'Ember Plastik Tertutup Medis / Non-Medis (50 Liter)', kategori: 'Peralatan Sanitasi & Kebersihan', merk: 'Clio Plastik / Lion Star 50L', satuan: 'Buah' },
            { kode: '1.3.2.03.01.02.001', nama: 'Kabel Listrik NYMHY 3x2.5mm', kategori: 'Perlengkapan Elektrikal & Sarpras', merk: 'Supreme Kabel Standar PLN', satuan: 'Meter' },
            { kode: '1.3.2.05.02.02.009', nama: 'Kain Sprei Kamar Rawat Inap Katun Polos', kategori: 'Linen & Perlengkapan Kamar', merk: 'Linen RS Putih Anti Noda', satuan: 'Lembar' }
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
                    satuan: 'Set',
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

        // Filter Daftar ASTAP Secara Real-time untuk Autocomplete
        getFilteredAstap(query) {
            if (!query || query.trim() === '') {
                return this.katalogAstap.slice(0, 8);
            }
            const q = query.toLowerCase().trim();
            return this.katalogAstap.filter(ast => 
                (ast.nama || '').toLowerCase().includes(q) || 
                (ast.kode || '').toLowerCase().includes(q) ||
                (ast.merk || '').toLowerCase().includes(q) ||
                (ast.kategori || '').toLowerCase().includes(q)
            );
        },

        // Pilih Barang dari Hasil Ketik Filter Dropdown
        selectAstapItem(item, ast) {
            item.nama_barang = ast.nama;
            item.kode_barang = ast.kode;
            item.merk_type = ast.merk;
            item.satuan = ast.satuan || 'Unit';
            this.activeDropdownIndex = null;
        },

        clearItemBarang(item, idx) {
            item.nama_barang = '';
            item.kode_barang = '';
            item.merk_type = '';
            item.satuan = 'Unit';
            if (idx !== undefined) {
                this.activeDropdownIndex = idx;
            }
        },

        // Auto Sync Satuan & Metadata saat input nama barang diketik manual
        onNamaBarangInput(item) {
            if (!item.nama_barang || item.nama_barang.trim() === '') {
                item.kode_barang = '';
                return;
            }
            const query = item.nama_barang.toLowerCase().trim();
            const exactMatch = this.katalogAstap.find(ast => 
                ast.nama.toLowerCase().trim() === query ||
                ast.kode.toLowerCase().trim() === query
            );
            if (exactMatch) {
                item.kode_barang = exactMatch.kode;
                item.merk_type = exactMatch.merk;
                item.satuan = exactMatch.satuan || 'Unit';
            }
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

        <!-- Top Navigation Bar (Bersih, Tanpa Tombol Simpan/Batal di Atas) -->
        <div class="flex items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl">
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
        </div>

        <!-- Form Card Container -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
            
            <!-- BAGIAN 1: INFORMASI TRANSAKSI & TUJUAN PENERIMA (AUTOFILL DATA UNIT) -->
            <div class="space-y-4">
                <h3 class="text-sm font-extrabold text-teal-300 uppercase tracking-wider flex items-center space-x-2 border-b border-slate-800 pb-3">
                    <span>1. Informasi Penyerahan & Pegawai Penerima Ruangan</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Kode Transaksi Distribusi -->
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">No. Registrasi Distribusi</label>
                        <input type="text" x-model="formData.kode" readonly
                               class="w-full bg-slate-950/80 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-teal-400 font-mono font-bold focus:outline-none cursor-not-allowed">
                    </div>

                    <!-- Nomor BAST Rujukan -->
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">No. BAST Distribusi</label>
                        <input type="text" x-model="formData.bast_nomor"
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white font-mono focus:outline-none focus:border-teal-500">
                    </div>

                    <!-- Tanggal Distribusi -->
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Tanggal Penyerahan</label>
                        <input type="date" x-model="formData.tgl"
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-teal-500">
                    </div>
                </div>

                <!-- Autocomplete Input Unit & Data PIC Penerima -->
                <div class="p-4 sm:p-5 rounded-2xl bg-slate-950 border border-slate-800 space-y-4">
                    
                    <!-- Search Unit Target -->
                    <div class="relative" @click.away="isSearchingUnit = false">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-slate-300 flex items-center space-x-1.5">
                                <span>🏥 Unit / Ruangan / Paviliun Tujuan</span>
                                <span class="text-teal-400 font-mono text-[11px]" x-text="'(' + unitList.length + ' Unit Terdaftar)'"></span>
                            </label>
                            <template x-if="formData.tujuan">
                                <button type="button" @click="clearUnit()" class="text-xs text-rose-400 hover:text-rose-300 font-semibold">
                                    ✕ Ganti Unit
                                </button>
                            </template>
                        </div>

                        <div class="relative">
                            <input type="text" x-model="unitSearch" 
                                   @focus="isSearchingUnit = true" 
                                   @input="isSearchingUnit = true" 
                                   placeholder="Ketik nama unit / ruangan (contoh: IGD, Melati, Radiologi, Bedah)..." 
                                   class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 pl-10 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-teal-500 transition-all font-semibold">
                            <svg class="w-4 h-4 text-teal-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <!-- Dropdown Autocomplete Unit (Dark Themed) -->
                        <div x-show="isSearchingUnit" 
                             x-transition 
                             class="absolute left-0 right-0 z-30 mt-1 bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl overflow-hidden max-h-56 overflow-y-auto divide-y divide-slate-800">
                            <template x-for="u in filteredUnitList" :key="u.id">
                                <div @click="selectUnit(u)" 
                                     class="p-3 hover:bg-teal-500/15 cursor-pointer transition-colors flex items-center justify-between group">
                                    <div>
                                        <p class="font-bold text-white text-xs group-hover:text-teal-300" x-text="u.nama"></p>
                                        <p class="text-[10px] text-slate-400" x-text="(u.kode || 'UNIT') + ' • ' + (u.tipe || 'Unit') + ' • PJ: ' + u.kepala"></p>
                                    </div>
                                    <span class="px-2 py-1 rounded bg-teal-500/20 text-teal-300 text-[10px] font-bold">Pilih &rarr;</span>
                                </div>
                            </template>
                            <template x-if="filteredUnitList.length === 0">
                                <div class="p-3 text-center text-xs text-slate-500">Unit tidak ditemukan</div>
                            </template>
                        </div>
                    </div>

                    <!-- Auto-filled PIC Penerima Details -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-slate-800/80">
                        <div>
                            <span class="text-[10px] text-slate-400 block font-semibold mb-1">Pegawai Penerima (Kepala/PJ)</span>
                            <input type="text" x-model="formData.penerima" placeholder="Terisi otomatis..." 
                                   class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2 text-xs text-emerald-400 font-bold focus:outline-none">
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block font-semibold mb-1">NIP Pegawai</span>
                            <input type="text" x-model="formData.penerima_nip" placeholder="Terisi otomatis..." 
                                   class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-300 font-mono focus:outline-none">
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block font-semibold mb-1">Jabatan Penerima</span>
                            <input type="text" x-model="formData.penerima_jabatan" placeholder="Terisi otomatis..." 
                                   class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-300 focus:outline-none">
                        </div>
                    </div>

                </div>
            </div>

            <!-- BAGIAN 2: DAFTAR BARANG YANG DIDISTRIBUSIKAN (MULTI-BARANG DALAM 1 TRANSAKSI) -->
            <div class="space-y-4 pt-2">
                <div class="border-b border-slate-800 pb-3">
                    <h3 class="text-sm font-extrabold text-teal-300 uppercase tracking-wider flex items-center space-x-2">
                        <span>2. Rincian Barang Aset yang Didistribusikan</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Ketik langsung nama barang untuk memfilter master ASTAP — Satuan, kode 108, dan spesifikasi terisi otomatis</p>
                </div>

                <!-- Daftar Input Multi-Barang (Layout Card Terstruktur & Rapi) -->
                <div class="space-y-5">
                    <template x-for="(item, idx) in formData.items" :key="item.id">
                        <div class="bg-slate-950/90 border border-slate-800 hover:border-slate-700/80 rounded-3xl p-5 sm:p-6 transition-all shadow-lg space-y-5">
                            
                            <!-- Card Header: Nomor Barang, Nama Terpilih Dinamis, Badge Kode 108 & Tombol Hapus -->
                            <div class="flex items-center justify-between pb-3 border-b border-slate-800 gap-3">
                                <div class="flex items-center space-x-3 min-w-0">
                                    <span class="w-7 h-7 rounded-xl bg-teal-500/20 text-teal-300 font-extrabold text-xs flex items-center justify-center border border-teal-500/30 shrink-0" x-text="idx + 1"></span>
                                    
                                    <!-- Judul Dinamis Mengikuti Barang yang Dipilih -->
                                    <span class="text-sm font-extrabold text-white tracking-wide truncate" 
                                          x-text="item.nama_barang ? item.nama_barang : ('Rincian Barang #' + (idx + 1))"></span>
                                    
                                    <!-- Badge Otomatis Kode 108 -->
                                    <template x-if="item.kode_barang">
                                        <span class="px-2.5 py-0.5 rounded-lg bg-slate-900 border border-cyan-500/30 text-cyan-400 font-mono font-bold text-[10px] shrink-0 hidden sm:inline-block" x-text="'Kode: ' + item.kode_barang"></span>
                                    </template>
                                </div>

                                <button type="button" @click="removeItem(idx)"
                                        class="px-3 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/30 text-xs font-semibold flex items-center space-x-1.5 transition-all active:scale-95 shrink-0"
                                        title="Hapus baris barang ini">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span>Hapus</span>
                                </button>
                            </div>

                            <!-- Grid Form Input Barang -->
                            <div class="space-y-4">
                                
                                <!-- Baris 1: Nama Barang & Kode Rekening 108 (Selalu Sejajar Berdampingan) -->
                                <div class="flex flex-row items-end gap-3 w-full">
                                    
                                    <!-- 1. Nama Barang / Aset (Autocomplete Search Langsung) -->
                                    <div class="flex-1 min-w-0 relative" @click.away="if (activeDropdownIndex === idx) activeDropdownIndex = null">
                                        <label class="block text-slate-300 font-semibold text-xs mb-1.5 flex items-center justify-between">
                                            <span>Nama Barang / Aset ASTAP</span>
                                            <span class="text-teal-400 font-mono text-[10px] hidden sm:inline">⚡ Ketik untuk filter</span>
                                        </label>
                                        <div class="relative">
                                            <input type="text" 
                                                   x-model="item.nama_barang" 
                                                   @focus="activeDropdownIndex = idx"
                                                   @input="activeDropdownIndex = idx; onNamaBarangInput(item)"
                                                   placeholder="Ketik nama barang aset (contoh: laptop, monitor, kasur, bed, pompa)..." 
                                                   class="w-full h-11 bg-slate-900 border border-slate-700/90 rounded-xl px-4 py-2.5 pl-10 pr-10 text-xs text-white font-bold placeholder-slate-500 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all">
                                            
                                            <svg class="w-4 h-4 text-teal-400 absolute left-3.5 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                            </svg>

                                            <template x-if="item.nama_barang && item.nama_barang.trim() !== ''">
                                                <button type="button" 
                                                        @click.stop="clearItemBarang(item, idx)" 
                                                        class="absolute right-2.5 top-2.5 z-20 w-6 h-6 rounded-lg bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 flex items-center justify-center transition-all cursor-pointer shadow-sm"
                                                        title="Kosongkan nama barang">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </template>
                                        </div>

                                        <!-- Floating Dropdown Hasil Ketik Filter ASTAP -->
                                        <div x-show="activeDropdownIndex === idx" 
                                             x-transition 
                                             class="absolute left-0 right-0 z-40 mt-1.5 bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl overflow-hidden max-h-60 overflow-y-auto divide-y divide-slate-800">
                                            
                                            <div class="px-4 py-2 bg-slate-950/90 text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between border-b border-slate-800">
                                                <span>Pilih Data Master ASTAP</span>
                                                <span class="text-teal-400 font-mono" x-text="getFilteredAstap(item.nama_barang).length + ' barang tersedia'"></span>
                                            </div>

                                            <template x-for="ast in getFilteredAstap(item.nama_barang)" :key="ast.kode">
                                                <div @click="selectAstapItem(item, ast)"
                                                     class="px-4 py-2.5 hover:bg-teal-500/15 cursor-pointer transition-colors group flex items-center justify-between gap-3">
                                                    <div class="space-y-0.5">
                                                        <p class="font-bold text-xs text-white group-hover:text-teal-300" x-text="ast.nama"></p>
                                                        <p class="text-[10px] text-slate-400" x-text="ast.kode + ' • ' + ast.kategori + (ast.merk ? ' • ' + ast.merk : '')"></p>
                                                    </div>
                                                    <span class="px-2.5 py-1 rounded-lg bg-slate-950 border border-slate-700 text-teal-300 font-mono text-[10px] font-bold shrink-0" x-text="ast.satuan"></span>
                                                </div>
                                            </template>

                                            <template x-if="getFilteredAstap(item.nama_barang).length === 0">
                                                <div class="p-4 text-center text-xs text-slate-400">
                                                    <p class="text-amber-400 font-semibold">Tidak ditemukan barang ASTAP</p>
                                                    <p class="text-[10px] text-slate-500 mt-0.5">Ketik nama lain atau isi nama barang secara manual</p>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- 2. Kode Rekening 108 (Sejajar di Samping Nama Barang) -->
                                    <div class="w-48 sm:w-56 shrink-0">
                                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Kode Rekening 108</label>
                                        <input type="text" x-model="item.kode_barang" placeholder="Terisi otomatis..."
                                               class="w-full h-11 bg-slate-900 border border-slate-700/90 rounded-xl px-4 py-2.5 text-xs text-cyan-300 font-mono font-bold placeholder-slate-500 focus:outline-none focus:border-teal-500 transition-all">
                                    </div>
                                </div>

                                <!-- Baris 2: Kondisi Fisik, Volume, & Satuan (Selalu Sejajar Berdampingan dalam 1 Baris) -->
                                <div class="flex flex-row items-end gap-3 w-full">
                                    
                                    <!-- 3. Kondisi Fisik Barang (Flex-1) -->
                                    <div class="flex-1 min-w-0">
                                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Kondisi Fisik Barang</label>
                                        <select x-model="item.kondisi" 
                                                class="w-full h-11 bg-slate-900 border border-slate-700/90 rounded-xl px-3.5 py-2.5 text-xs text-emerald-400 font-bold focus:outline-none focus:border-teal-500 transition-all cursor-pointer">
                                            <option value="Baik">🟢 Baik (Siap Pakai)</option>
                                            <option value="Kurang Baik">🟡 Kurang Baik (Perlu Servis)</option>
                                            <option value="Rusak">🔴 Rusak</option>
                                        </select>
                                    </div>

                                    <!-- 4. Volume (Qty) (Kecil) -->
                                    <div class="w-28 sm:w-32 shrink-0">
                                        <label class="block text-slate-300 font-semibold text-xs mb-1.5 text-center">Volume (Qty)</label>
                                        <input type="number" min="1" 
                                               x-model="item.qty" 
                                               class="w-full h-11 bg-slate-900 border border-slate-700/90 rounded-xl px-3 py-2.5 text-xs text-center text-white font-mono font-bold focus:outline-none focus:border-teal-500 transition-all">
                                    </div>

                                    <!-- 5. Satuan (Kompak - Otomatis ASTAP) -->
                                    <div class="w-28 sm:w-32 shrink-0">
                                        <label class="block text-teal-300 font-semibold text-xs mb-1.5 text-center">Satuan (⚡ Auto)</label>
                                        <input type="text" 
                                               x-model="item.satuan" 
                                               placeholder="Unit" 
                                               class="w-full h-11 bg-slate-900 border border-teal-500/50 rounded-xl px-3 py-2.5 text-xs text-center text-teal-300 font-bold focus:outline-none focus:border-teal-500 transition-all">
                                    </div>
                                </div>

                                <!-- Baris 3: Keterangan / Catatan Spesifik Item (Sendiri / Full-Width) -->
                                <div>
                                    <label class="block text-slate-400 font-semibold text-xs mb-1.5">Keterangan / Catatan Peruntukan Barang (Opsional)</label>
                                    <input type="text" 
                                           x-model="item.keterangan" 
                                           placeholder="Contoh: u/ Ruang Tindakan IGD / Bed No. 04 / Pengadaan DAK Kesehatan..." 
                                           class="w-full h-11 bg-slate-900 border border-slate-700/90 rounded-xl px-4 py-2.5 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-teal-500 transition-all">
                                </div>

                            </div>

                        </div>
                    </template>
                </div>

                <!-- Ringkasan Akumulasi Volume Multi-Barang & Tombol Tambah Bawah -->
                <div class="mt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 bg-slate-950 rounded-2xl border border-slate-800 text-xs">
                    <div class="flex items-center space-x-3 text-slate-300">
                        <span>Total Rincian: <strong class="text-teal-400 font-extrabold" x-text="formData.items.length + ' Jenis Barang'"></strong></span>
                        <span>•</span>
                        <span>Akumulasi Volume: <strong class="text-emerald-400 font-extrabold" x-text="getTotalItemVolume() + ' Total Item/Unit'"></strong></span>
                    </div>

                    <button type="button" @click="addItem()" 
                            class="px-4 py-2 rounded-xl bg-teal-500/20 hover:bg-teal-500/30 text-teal-300 border border-teal-500/40 font-bold flex items-center space-x-2 transition-all active:scale-95 self-start sm:self-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah Barang Lagi</span>
                    </button>
                </div>
            </div>

            <!-- BAGIAN 3: CATATAN UMUM PENEMPATAN -->
            <div class="pt-4 border-t border-slate-800">
                <label class="block text-slate-300 font-semibold text-xs mb-1.5">Catatan Umum / Keterangan Penempatan</label>
                <textarea x-model="formData.keterangan" rows="2" placeholder="Contoh: Pengadaan DAK Kesehatan / BLUD untuk kelengkapan ruangan..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-teal-500"></textarea>
            </div>

            <!-- Tombol Aksi Batal & Simpan (Hanya di Bagian Bawah Form Sesuai Permintaan) -->
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
