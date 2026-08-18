<x-layout title="Data ASTAP - SIMAT-RK">
    @section('page-title', 'Data ASTAP')
    @section('breadcrumb', 'Master Utama / Data ASTAP')

    <div x-data="{
        searchQuery: '',
        categoryFilter: 'all',
        kondisiFilter: 'all',
        asalUsulFilter: 'all',
        tahunFilter: 'all',
        showAddModal: false,
        showEditModal: false,
        showDetailModal: false,
        selectedAstap: null,
        selectedAstapDetail: null,
        
        // Multi-Step Form State
        currentStep: 1,
        totalSteps: 4,
        
        // Form Data Model
        formData: {
            category: 'KIB A',
            jenis_aset_kode: '1.3.1',
            jenis_aset_nama: 'TANAH',
            sub_rincian_kode: '1.3.1.01.01.01',
            sub_rincian_nama: 'TANAH BANGUNAN PERUMAHAN/G.TEMPAT TINGGAL',
            kode_barang: '1.3.1.01.01.01.001',
            nama_barang: '',
            volume_satuan: '1,5 Hektar (15.000 m²)',
            
            // Blok 1: Penganggaran & Akuntansi
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
            
            // Blok 2: Dokumen Pembelian & Pengadaan
            spk_nomor: '',
            spk_tanggal: '',
            surat_pesanan_nomor: '',
            surat_pesanan_tanggal: '',
            kwitansi_nomor: '',
            kwitansi_tanggal: '',
            faktur_nomor: '',
            faktur_tanggal: '',
            
            // Blok 3: Spesifikasi Teknis
            luas_tanah: '',
            tahun_pengadaan: new Date().getFullYear(),
            letak_lokasi: '',
            hak_tanah: 'Hak Pakai',
            sertifikat_nomor: '',
            sertifikat_tanggal: '',
            penggunaan: 'Bangunan Rumah Sakit & Fasilitas Kesehatan',
            asal_usul: 'APBD Kabupaten',
            harga_satuan: '',
            kondisi: 'Baik',
            keterangan: '',
            
            // Non-tanah extras
            merk: '',
            type: '',
            ukuran: '',
            bahan: '',
            no_pabrik: ''
        },

        selectCategory(kib, kode, nama) {
            this.formData.category = kib;
            this.formData.jenis_aset_kode = kode;
            this.formData.jenis_aset_nama = nama;
        },

        resetModal() {
            this.currentStep = 1;
            this.showAddModal = false;
        },

        openDetail(item) {
            this.selectedAstapDetail = item;
            this.showDetailModal = true;
        },

        resetFilters() {
            this.searchQuery = '';
            this.categoryFilter = 'all';
            this.kondisiFilter = 'all';
            this.asalUsulFilter = 'all';
            this.tahunFilter = 'all';
        },

        astaps: [
            {
                id: 1,
                category: 'KIB B',
                kode_barang: '1.3.2.01.03.05.005',
                nama_barang: 'Submersible Pump 7.5 HP',
                tahun_perolehan: '2025',
                volume_satuan: '1 Unit (Pompa 7.5 HP)',
                jenis_aset_nama: 'PERALATAN DAN MESIN',
                merk: 'Franklin Electric',
                type: '2347288602G',
                ukuran: '7,5hp, 3phase, max 139 kedalaman',
                no_pabrik: '23K14-17-0006',
                bahan: 'Campuran Baja',
                program_nama: 'Program Penunjang Urusan Pemerintah Daerah',
                kegiatan_nama: 'Peningkatan Pelayanan BLUD',
                sub_kegiatan_nama: 'Pelayanan dan Penunjang Pelayanan BLUD',
                rekening_nama: 'Belanja Modal Pompa',
                spk_nomor: '019/SPK-PMP/VI/2025',
                spk_tanggal: '2025-06-01',
                surat_pesanan_nomor: '019/BN.BA/VI/2025',
                surat_pesanan_tanggal: '2025-06-04',
                kwitansi_nomor: 'KW-019/PMP/2025',
                faktur_nomor: 'INV-2025-091',
                jumlah_realisasi: 'Rp 42.501.900',
                kondisi: 'Baik',
                asal_usul: 'BLUD RSUD',
                keterangan: 'Operasional normal pompa cadangan instalasi air bersih rumah sakit'
            },
            {
                id: 2,
                category: 'KIB B',
                kode_barang: '1.3.2.01.03.05.010',
                nama_barang: 'Pompa Air Shimizu PS-130',
                tahun_perolehan: '2025',
                volume_satuan: '1 Unit (Daya Dorong 35m)',
                jenis_aset_nama: 'PERALATAN DAN MESIN',
                merk: 'Shimizu',
                type: 'PS-130 BIT',
                ukuran: 'Daya hisap 9m & dorong 35m',
                no_pabrik: 'PS-130 BIT-001',
                bahan: 'Campuran Besi',
                program_nama: 'Program Penunjang Urusan Pemerintah Daerah',
                kegiatan_nama: 'Peningkatan Pelayanan BLUD',
                sub_kegiatan_nama: 'Pelayanan dan Penunjang Pelayanan BLUD',
                rekening_nama: 'Belanja Modal Pompa',
                spk_nomor: '019/SPK-PMP/VI/2025',
                spk_tanggal: '2025-06-01',
                surat_pesanan_nomor: '019/BN.BA/VI/2025',
                surat_pesanan_tanggal: '2025-06-04',
                kwitansi_nomor: 'KW-020/PMP/2025',
                faktur_nomor: 'INV-2025-092',
                jumlah_realisasi: 'Rp 42.501.900',
                kondisi: 'Baik',
                asal_usul: 'BLUD RSUD',
                keterangan: 'Pendorong air ke tandon instalasi gizi dan gizi klinik'
            },
            {
                id: 3,
                category: 'KIB B',
                kode_barang: '1.3.2.02.01.01.005',
                nama_barang: 'CT-Scan 128 Slice High Resolution',
                tahun_perolehan: '2024',
                volume_satuan: '1 Unit (128-Slice System)',
                jenis_aset_nama: 'PERALATAN DAN MESIN',
                merk: 'Siemens SOMATOM',
                type: '128-Slice Perspective',
                ukuran: 'Unit Radiologi Medis Lengkap',
                no_pabrik: 'SN-99812-RAD',
                bahan: 'Logam & Elektronik Medis',
                program_nama: 'Program Pengadaan Sarana & Prasarana Kesehatan',
                kegiatan_nama: 'Pengembangan Fasilitas Radiologi',
                sub_kegiatan_nama: 'Pengadaan Alat Kedokteran Radiologi',
                rekening_nama: 'Belanja Modal Alat Kedokteran Radiologi',
                spk_nomor: '045/SPK-RAD/VII/2024',
                spk_tanggal: '2024-07-10',
                surat_pesanan_nomor: '045/RSUD/VII/2024',
                surat_pesanan_tanggal: '2024-07-15',
                kwitansi_nomor: 'KW-045/RAD/2024',
                faktur_nomor: 'INV-RAD-2024-01',
                jumlah_realisasi: 'Rp 1.450.000.000',
                kondisi: 'Baik',
                asal_usul: 'DAK Kesehatan',
                keterangan: 'Telah terkalibrasi BAPETEN dan siap operasional 24 Jam'
            },
            {
                id: 4,
                category: 'KIB A',
                kode_barang: '1.3.1.01.01.02.013',
                nama_barang: 'Lahan Bangunan RSUD Dr. H. Koesnandi',
                tahun_perolehan: '1984',
                volume_satuan: '3,54 Hektar (35.400 m²)',
                jenis_aset_nama: 'TANAH',
                merk: '-',
                type: 'Sertifikat Hak Pakai No. 108',
                ukuran: '35.400 m²',
                no_pabrik: 'HP-108/1984',
                bahan: 'Tanah Matang Siap Bangun',
                program_nama: 'Pengelolaan BMD Tanah Pemkab',
                kegiatan_nama: 'Pengamanan Aset Tanah Rumah Sakit',
                sub_kegiatan_nama: 'Sertifikasi dan Pengamanan Fisik',
                rekening_nama: 'Belanja Modal Tanah',
                spk_nomor: 'SK-BPN/1984/01',
                spk_tanggal: '1984-03-12',
                surat_pesanan_nomor: 'SK-BPN/1984',
                surat_pesanan_tanggal: '1984-03-15',
                kwitansi_nomor: 'KW-TNH-1984',
                faktur_nomor: 'DOK-BPN-1984',
                jumlah_realisasi: 'Rp 8.500.000.000',
                kondisi: 'Baik',
                asal_usul: 'APBD Kabupaten',
                keterangan: 'Batas lahan terpagar penuh dan sertifikat hak pakai aktif'
            },
            {
                id: 5,
                category: 'KIB C',
                kode_barang: '1.3.3.01.01.01.008',
                nama_barang: 'Gedung Paviliun Graha Amukti VIP',
                tahun_perolehan: '2018',
                volume_satuan: '2.800 m² (Gedung 2 Lantai)',
                jenis_aset_nama: 'GEDUNG DAN BANGUNAN',
                merk: 'Konstruksi Beton Bertingkat',
                type: 'Gedung Rawat Inap VIP 2 Lantai',
                ukuran: '2.800 m²',
                no_pabrik: 'IMB-2018/09',
                bahan: 'Beton Bertulang & Baja',
                program_nama: 'Peningkatan Sarana Ruang Rawat Inap',
                kegiatan_nama: 'Pembangunan Paviliun VIP',
                sub_kegiatan_nama: 'Pembangunan Gedung Graha Amukti',
                rekening_nama: 'Belanja Modal Gedung Rawat Inap',
                spk_nomor: 'SPK-GRH/2018/01',
                spk_tanggal: '2018-02-10',
                surat_pesanan_nomor: 'SPK-GRH/2018',
                surat_pesanan_tanggal: '2018-02-15',
                kwitansi_nomor: 'KW-GRH-2018',
                faktur_nomor: 'INV-GRH-2018',
                jumlah_realisasi: 'Rp 4.200.000.000',
                kondisi: 'Baik',
                asal_usul: 'APBD Kabupaten',
                keterangan: 'Kapasitas 24 kamar VIP & VVIP dengan fasilitas terintegrasi'
            },
            {
                id: 6,
                category: 'KIB D',
                kode_barang: '1.3.4.03.01.01.004',
                nama_barang: 'Jaringan Pipa Oksigen Sentral Medis',
                tahun_perolehan: '2020',
                volume_satuan: '1.200 Meter Lari',
                jenis_aset_nama: 'Aset Jalan, Irigasi dan Jaringan',
                merk: 'Copper Pipe Medical Grade',
                type: 'Instalasi Oksigen Medis Terpusat',
                ukuran: '1.200 meter lari',
                no_pabrik: 'INS-OKS-2020',
                bahan: 'Tembaga Medis Standard Hospital',
                program_nama: 'Peningkatan Jaringan Utilitas Medis',
                kegiatan_nama: 'Instalasi Pipa Gas Medis',
                sub_kegiatan_nama: 'Pemasangan Outlet Oksigen Sentral',
                rekening_nama: 'Belanja Modal Jaringan Utilitas',
                spk_nomor: 'SPK-OKS/2020/08',
                spk_tanggal: '2020-05-14',
                surat_pesanan_nomor: 'SPK-OKS/2020',
                surat_pesanan_tanggal: '2020-05-20',
                kwitansi_nomor: 'KW-OKS-2020',
                faktur_nomor: 'INV-OKS-2020',
                jumlah_realisasi: 'Rp 650.000.000',
                kondisi: 'Rusak Ringan',
                asal_usul: 'BLUD RSUD',
                keterangan: 'Perlu penggantian seal valve dan flow meter di zona IGD'
            },
            {
                id: 7,
                category: 'KIB E',
                kode_barang: '1.3.5.01.01.01.002',
                nama_barang: 'Buku Jurnal Kedokteran & Farmakologi',
                tahun_perolehan: '2021',
                volume_satuan: '50 Judul (Vol 1-12)',
                jenis_aset_nama: 'Aset Tetap Lainnya',
                merk: 'Elsevier / PubMed Press',
                type: 'Hardcover Vol 1-12 Lengkap',
                ukuran: 'Edisi Internasional 2021',
                no_pabrik: 'ISBN-978-01-229',
                bahan: 'Kertas Art Paper Hardcover',
                program_nama: 'Peningkatan Riset & Pendidikan Kedokteran',
                kegiatan_nama: 'Pengadaan Bahan Pustaka Medis',
                sub_kegiatan_nama: 'Langganan Jurnal Ilmiah',
                rekening_nama: 'Belanja Modal Buku Perpustakaan',
                spk_nomor: '012/SPK-BKO/2021',
                spk_tanggal: '2021-08-01',
                surat_pesanan_nomor: '012/PERPUS/2021',
                surat_pesanan_tanggal: '2021-08-05',
                kwitansi_nomor: 'KW-BKO-2021',
                faktur_nomor: 'INV-BKO-2021',
                jumlah_realisasi: 'Rp 85.000.000',
                kondisi: 'Baik',
                asal_usul: 'BLUD RSUD',
                keterangan: 'Tersedia untuk referensi dokter spesialis, residen & perawat'
            },
            {
                id: 8,
                category: 'KIB F',
                kode_barang: '1.3.6.01.01.01.001',
                nama_barang: 'Pembangunan Gedung Rawat Inap Terpadu Lt 3',
                tahun_perolehan: '2026',
                volume_satuan: '3.200 m² (Fisik 60%)',
                jenis_aset_nama: 'Aset Konstruksi Dalam Pengerjaan',
                merk: 'Konstruksi Baja & Beton Bertulang',
                type: 'Bangunan Gedung KDP',
                ukuran: '3.200 m²',
                no_pabrik: 'KDP-GRH-2026',
                bahan: 'Struktur Beton Bertulang K-350',
                program_nama: 'Pembangunan Infrastruktur Fasilitas Kesehatan',
                kegiatan_nama: 'Pembangunan Ruang Rawat Inap Baru',
                sub_kegiatan_nama: 'Konstruksi Fisik Gedung Baru Lt 3',
                rekening_nama: 'Belanja Modal Gedung & Bangunan (KDP)',
                spk_nomor: '015/SPK-KDP/2026',
                spk_tanggal: '2026-01-10',
                surat_pesanan_nomor: '015/KDP/2026',
                surat_pesanan_tanggal: '2026-01-15',
                kwitansi_nomor: 'KW-KDP-2026',
                faktur_nomor: 'INV-KDP-2026',
                jumlah_realisasi: 'Rp 3.500.000.000',
                kondisi: 'Dalam Renovasi',
                asal_usul: 'APBD Kabupaten',
                keterangan: 'Progres fisik konstruksi 60%, target rampung akhir tahun 2026'
            },
            {
                id: 9,
                category: 'KIB G',
                kode_barang: '1.5.3.01.01.01.005',
                nama_barang: 'Software SIMRS Terintegrasi & EMR Cloud',
                tahun_perolehan: '2024',
                volume_satuan: '1 Lisensi Sistem (Unlimited)',
                jenis_aset_nama: 'Aset Tidak Berwujud',
                merk: 'SIMAT Health Enterprise',
                type: 'Enterprise Server License V4.2',
                ukuran: 'Modul Billing, Farmasi, EMR & Rekam Medis',
                no_pabrik: 'LIC-SIMRS-2024',
                bahan: 'Hak Cipta / Lisensi Software Permanen',
                program_nama: 'Digitalisasi Pelayanan Kesehatan Rumah Sakit',
                kegiatan_nama: 'Implementasi Rekam Medis Elektronik (RME)',
                sub_kegiatan_nama: 'Pengadaan Lisensi Software SIMRS',
                rekening_nama: 'Belanja Modal Aset Tidak Berwujud',
                spk_nomor: '077/SPK-SIMRS/2024',
                spk_tanggal: '2024-03-20',
                surat_pesanan_nomor: '077/SIMRS/2024',
                surat_pesanan_tanggal: '2024-03-25',
                kwitansi_nomor: 'KW-SIMRS-2024',
                faktur_nomor: 'INV-SIMRS-2024',
                jumlah_realisasi: 'Rp 450.000.000',
                kondisi: 'Baik',
                asal_usul: 'BLUD RSUD',
                keterangan: 'Lisensi aktif selamanya dan terintegrasi SatuSehat Kemenkes RI'
            },
            {
                id: 10,
                category: 'KIB H',
                kode_barang: '1.3.7.01.01.01.002',
                nama_barang: 'Rehabilitasi & Renovasi Gedung Poliklinik Lt 2',
                tahun_perolehan: '2025',
                volume_satuan: '1.500 m² (Area Lt 2)',
                jenis_aset_nama: 'Aset Tetap Dalam Renovasi',
                merk: 'Renovasi Fisik & Interior Poli Spesialis',
                type: 'Proyek Tahap II Renovasi',
                ukuran: '1.500 m²',
                no_pabrik: 'PRJ-RNV-2025',
                bahan: 'Beton, Partisi Gypsum & Baja Ringan',
                program_nama: 'Program Peningkatan Pelayanan Poliklinik',
                kegiatan_nama: 'Renovasi Gedung Poliklinik',
                sub_kegiatan_nama: 'Pekerjaan Fisik Interior Lt 2',
                rekening_nama: 'Belanja Modal Renovasi Bangunan',
                spk_nomor: '088/SPK-RNV/2025',
                spk_tanggal: '2025-04-12',
                surat_pesanan_nomor: '088/SPK-RNV/2025',
                surat_pesanan_tanggal: '2025-04-15',
                kwitansi_nomor: 'KW-RNV-2025',
                faktur_nomor: 'INV-RNV-2025',
                jumlah_realisasi: 'Rp 950.000.000',
                kondisi: 'Dalam Renovasi',
                asal_usul: 'APBD Kabupaten',
                keterangan: 'Progres fisik 75%, target selesai dan serah terima September 2026'
            }
        ],

        get filteredAstaps() {
            const query = (this.searchQuery || '').toLowerCase();
            return this.astaps.filter(item => {
                const matchSearch = (item.nama_barang || '').toLowerCase().includes(query) || 
                                    (item.volume_satuan || '').toLowerCase().includes(query) ||
                                    (item.tahun_perolehan || '').toLowerCase().includes(query) ||
                                    (item.kode_barang || '').toLowerCase().includes(query) ||
                                    (item.merk || '').toLowerCase().includes(query);
                                    
                const matchCategory = this.categoryFilter === 'all' || item.category === this.categoryFilter;
                const matchKondisi = this.kondisiFilter === 'all' || item.kondisi === this.kondisiFilter;
                const matchAsalUsul = this.asalUsulFilter === 'all' || item.asal_usul === this.asalUsulFilter;
                const matchTahun = this.tahunFilter === 'all' || item.tahun_perolehan === this.tahunFilter;
                
                return matchSearch && matchCategory && matchKondisi && matchAsalUsul && matchTahun;
            });
        },

        downloadQrCode(kode, nama) {
            alert('📱 Mendownload QR Code Aset: ' + kode + '\nNama: ' + nama);
        },

        downloadExcel() {
            alert('📊 Mendownload Keseluruhan Katalog Data ASTAP (.xlsx Format Sesuai Standar KIB)...');
        },

        openEdit(item) {
            this.selectedAstap = { ...item };
            this.showEditModal = true;
        }
    }" x-cloak>

        <!-- ========================================================================= -->
        <!-- HEADER BANNER & STATISTIK RINGKAS                                         -->
        <!-- ========================================================================= -->
        <div class="bg-gradient-to-r from-emerald-600/15 via-slate-900 to-slate-900 border border-emerald-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-6 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold mb-3">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>KATALOG INVENTARIS ASET TETAP RSUD DR. H. KOESNANDI</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Katalog Data ASTAP</h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                        Pencatatan daftar aset tetap rumah sakit, tahun perolehan, lokasi penempatan unit/paviliun, nilai realisasi anggaran, serta status kondisi terkini.
                    </p>
                </div>
                
                <!-- Aksi Utama -->
                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    <button type="button" @click="downloadExcel()"
                        class="px-4 py-2.5 rounded-xl bg-slate-900/80 hover:bg-slate-800 text-emerald-400 border border-emerald-500/40 font-bold text-xs shadow-lg transition-all flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Export Excel</span>
                    </button>

                    <a href="{{ route('astap.create') }}"
                        class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah ASTAP</span>
                    </a>
                </div>
            </div>

            <!-- Mini Summary KPI Cards Strip -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-800/80">
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-lg">📦</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Aset</span>
                        <span class="text-sm sm:text-base font-extrabold text-white" x-text="astaps.length + ' Item'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400 text-lg">💰</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Investasi</span>
                        <span class="text-sm sm:text-base font-extrabold text-emerald-400 font-mono">Rp 15,87 M</span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-teal-500/10 text-teal-400 text-lg">🟢</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Kondisi Baik</span>
                        <span class="text-sm sm:text-base font-extrabold text-teal-300">75% (6 Aset)</span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-purple-500/10 text-purple-400 text-lg">🏥</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Unit Tersebar</span>
                        <span class="text-sm sm:text-base font-extrabold text-purple-300">6 Lokasi RSUD</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- AREA PEMFILTERAN & PENCARIAN (FULL WIDTH, RAPID & BERSIH)                 -->
        <!-- ========================================================================= -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl mb-6">
            <div class="flex flex-col gap-4">
                
                <!-- Quick Filter Category Pills (Wrapping & Always Visible) -->
                <div class="flex flex-wrap items-center gap-2 pt-1 text-xs">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-1 shrink-0">KIB:</span>
                    <button type="button" @click="categoryFilter = 'all'"
                        :class="categoryFilter === 'all' ? 'bg-emerald-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        Semua KIB
                    </button>
                    <button type="button" @click="categoryFilter = 'KIB A'"
                        :class="categoryFilter === 'KIB A' ? 'bg-amber-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        🌾 KIB A (Tanah)
                    </button>
                    <button type="button" @click="categoryFilter = 'KIB B'"
                        :class="categoryFilter === 'KIB B' ? 'bg-cyan-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        🔬 KIB B (Mesin)
                    </button>
                    <button type="button" @click="categoryFilter = 'KIB C'"
                        :class="categoryFilter === 'KIB C' ? 'bg-purple-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        🏢 KIB C (Gedung)
                    </button>
                    <button type="button" @click="categoryFilter = 'KIB D'"
                        :class="categoryFilter === 'KIB D' ? 'bg-teal-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        🚰 KIB D (Jaringan)
                    </button>
                    <button type="button" @click="categoryFilter = 'KIB E'"
                        :class="categoryFilter === 'KIB E' ? 'bg-orange-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        📦 KIB E (Lainnya)
                    </button>
                    <button type="button" @click="categoryFilter = 'KIB F'"
                        :class="categoryFilter === 'KIB F' ? 'bg-rose-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        🏗️ KIB F (KDP)
                    </button>
                    <button type="button" @click="categoryFilter = 'KIB G'"
                        :class="categoryFilter === 'KIB G' ? 'bg-indigo-500 text-white font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        💾 KIB G (Tidak Berwujud)
                    </button>
                    <button type="button" @click="categoryFilter = 'KIB H'"
                        :class="categoryFilter === 'KIB H' ? 'bg-pink-500 text-white font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        🔨 KIB H (Renovasi)
                    </button>
                </div>

                <!-- Live Search Bar + Counter -->
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full pt-2 border-t border-slate-800/80">
                    <div class="relative flex-1 w-full">
                        <input type="text" x-model="searchQuery" placeholder="Cari nama barang / lokasi penempatan / tahun / merk..."
                            class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition-all">
                        <svg class="w-4 h-4 text-emerald-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3.5 top-3 text-slate-500 hover:text-white text-xs font-bold">&times;</button>
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <span class="px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-[11px] font-semibold text-slate-300">
                            Menampilkan <span class="text-emerald-400 font-bold" x-text="filteredAstaps.length"></span> dari <span class="text-white font-bold" x-text="astaps.length"></span> Data
                        </span>
                        <button type="button" @click="resetFilters()"
                            class="px-3 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white text-xs font-semibold border border-slate-700 transition-all">
                            🔄 Reset
                        </button>
                    </div>
                </div>

                <!-- Grid Dropdown Filters (4 Kolom Lengkap Termasuk Kategori KIB) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-3 border-t border-slate-800/80">
                    <!-- Filter Kategori KIB -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Klasifikasi KIB</label>
                        <select x-model="categoryFilter" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-emerald-500">
                            <option value="all">Semua KIB (A - H)</option>
                            <option value="KIB A">KIB A - Aset Tanah (1.3.1)</option>
                            <option value="KIB B">KIB B - Aset Peralatan & Mesin (1.3.2)</option>
                            <option value="KIB C">KIB C - Aset Gedung & Bangunan (1.3.3)</option>
                            <option value="KIB D">KIB D - Aset Jalan, Irigasi & Jaringan (1.3.4)</option>
                            <option value="KIB E">KIB E - Aset Tetap Lainnya (1.3.5)</option>
                            <option value="KIB F">KIB F - Aset Konstruksi KDP (1.3.6)</option>
                            <option value="KIB G">KIB G - Aset Tidak Berwujud (1.5.3)</option>
                            <option value="KIB H">KIB H - Aset Tetap Dalam Renovasi (1.3.7)</option>
                        </select>
                    </div>

                    <!-- Filter Sumber Dana / Asal Usul -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Sumber Dana / Asal Usul</label>
                        <select x-model="asalUsulFilter" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-emerald-500">
                            <option value="all">Semua Sumber Dana</option>
                            <option value="BLUD RSUD">BLUD RSUD</option>
                            <option value="APBD Kabupaten">APBD Kabupaten</option>
                            <option value="DAK Kesehatan">DAK Kesehatan</option>
                        </select>
                    </div>

                    <!-- Filter Kondisi -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kondisi Aset</label>
                        <select x-model="kondisiFilter" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-emerald-500">
                            <option value="all">Semua Kondisi</option>
                            <option value="Baik">Baik (B)</option>
                            <option value="Rusak Ringan">Rusak Ringan (RR)</option>
                            <option value="Rusak Berat">Rusak Berat (RB)</option>
                            <option value="Dalam Renovasi">Dalam Renovasi</option>
                        </select>
                    </div>

                    <!-- Filter Tahun -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tahun Perolehan</label>
                        <select x-model="tahunFilter" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-emerald-500">
                            <option value="all">Semua Tahun Masuk</option>
                            <option value="2026">2026</option>
                            <option value="2025">2025</option>
                            <option value="2024">2024</option>
                            <option value="2021">2021</option>
                            <option value="2020">2020</option>
                            <option value="2018">2018</option>
                            <option value="1984">1984</option>
                        </select>
                    </div>
                </div>

            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- TABEL KATALOG DATA ASTAP                                                  -->
        <!-- ========================================================================= -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6 overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 text-center w-12">No</th>
                        <th class="px-4 py-3.5 text-center">Nama Barang / ASTAP</th>
                        <th class="px-4 py-3.5 text-center">Tahun Masuk</th>
                        <th class="px-4 py-3.5 text-center">Volume / Kuantitas / Luas</th>
                        <th class="px-4 py-3.5 text-center">Nilai Realisasi</th>
                        <th class="px-4 py-3.5 text-center">Kondisi</th>
                        <th class="px-4 py-3.5 text-center">QR Code</th>
                        <th class="px-4 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    <template x-for="(item, index) in filteredAstaps" :key="item.id">
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <!-- Nomor Urut 1, 2, 3... -->
                            <td class="px-4 py-4 text-center font-bold text-slate-400" x-text="index + 1"></td>
                            
                            <!-- Nama Barang / ASTAP -->
                            <td class="px-4 py-4">
                                <div class="font-bold text-white text-sm" x-text="item.nama_barang"></div>
                                <div class="flex items-center space-x-1.5 mt-1">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold"
                                        :class="{
                                            'bg-amber-500/20 text-amber-300 border border-amber-500/30': item.category === 'KIB A',
                                            'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30': item.category === 'KIB B',
                                            'bg-purple-500/20 text-purple-300 border border-purple-500/30': item.category === 'KIB C',
                                            'bg-teal-500/20 text-teal-300 border border-teal-500/30': item.category === 'KIB D',
                                            'bg-orange-500/20 text-orange-300 border border-orange-500/30': item.category === 'KIB E',
                                            'bg-rose-500/20 text-rose-300 border border-rose-500/30': item.category === 'KIB F',
                                            'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30': item.category === 'KIB G',
                                            'bg-pink-500/20 text-pink-300 border border-pink-500/30': item.category === 'KIB H'
                                        }"
                                        x-text="item.category"></span>
                                    <span class="text-[11px] text-slate-400" x-text="item.jenis_aset_nama"></span>
                                </div>
                            </td>

                            <!-- Tahun Masuk / Perolehan -->
                            <td class="px-4 py-4 text-center font-mono font-bold text-slate-200" x-text="item.tahun_perolehan"></td>

                            <!-- Volume / Kuantitas / Luas Aset -->
                            <td class="px-4 py-4 text-center">
                                <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-xl bg-slate-950 border border-slate-800 text-teal-300 font-semibold font-mono text-xs">
                                    <span>📏</span>
                                    <span x-text="item.volume_satuan"></span>
                                </div>
                            </td>

                            <!-- Nilai Realisasi -->
                            <td class="px-4 py-4 text-center font-bold text-emerald-400 font-mono" x-text="item.jumlah_realisasi"></td>

                            <!-- Kondisi -->
                            <td class="px-4 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold"
                                      :class="{
                                          'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30': item.kondisi === 'Baik',
                                          'bg-amber-500/20 text-amber-300 border border-amber-500/30': item.kondisi === 'Rusak Ringan',
                                          'bg-rose-500/20 text-rose-300 border border-rose-500/30': item.kondisi === 'Rusak Berat',
                                          'bg-purple-500/20 text-purple-300 border border-purple-500/30': item.kondisi === 'Dalam Renovasi'
                                      }"
                                      x-text="item.kondisi"></span>
                            </td>

                            <!-- Download QR Code Button -->
                            <td class="px-4 py-4 text-center">
                                <button type="button" @click="downloadQrCode(item.kode_barang, item.nama_barang)"
                                    class="px-2.5 py-1.5 rounded-xl bg-teal-500/10 hover:bg-teal-500/20 text-teal-300 border border-teal-500/30 text-[11px] font-semibold transition-all inline-flex items-center space-x-1.5 shadow-sm"
                                    title="Download Label QR Code">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                    <span>QR</span>
                                </button>
                            </td>

                            <!-- Aksi: Detail, Ubah, Hapus (Seragam & Rapi) -->
                            <td class="px-4 py-4 text-center space-x-1 whitespace-nowrap">
                                <button type="button" @click="openDetail(item)"
                                    class="px-2.5 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-300 hover:bg-emerald-500/20 border border-emerald-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Detail</span>
                                </button>

                                <a :href="'/astap/' + item.id + '/edit'" 
                                    class="px-2.5 py-1.5 rounded-xl bg-cyan-500/10 text-cyan-300 hover:bg-cyan-500/20 border border-cyan-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>Ubah</span>
                                </a>

                                <button type="button" 
                                    class="px-2.5 py-1.5 rounded-xl bg-rose-500/10 text-rose-300 hover:bg-rose-500/20 border border-rose-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span>Hapus</span>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL DETAIL KESELURUHAN ASET ASTAP                                       -->
        <!-- ========================================================================= -->
        <div x-show="showDetailModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4 sm:p-6">
            <div @click.away="showDetailModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-2xl w-full shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                
                <!-- Modal Detail Header -->
                <div class="p-6 border-b border-slate-800 bg-slate-950/60 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 font-bold text-lg">
                            🔍
                        </div>
                        <div>
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30" x-text="selectedAstapDetail ? selectedAstapDetail.category : ''"></span>
                                <span class="text-xs font-mono text-slate-400" x-text="selectedAstapDetail ? selectedAstapDetail.kode_barang : ''"></span>
                            </div>
                            <h3 class="text-base font-extrabold text-white" x-text="selectedAstapDetail ? selectedAstapDetail.nama_barang : ''"></h3>
                        </div>
                    </div>
                    <button type="button" @click="showDetailModal = false" class="text-slate-500 hover:text-white text-2xl font-bold">&times;</button>
                </div>

                <!-- Modal Detail Body -->
                <div class="p-6 overflow-y-auto flex-1 text-xs space-y-5" x-if="selectedAstapDetail">
                    
                    <!-- Status Ringkas -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="bg-slate-950/60 border border-slate-800 rounded-2xl p-3">
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Tahun Masuk</span>
                            <p class="text-sm font-bold text-white mt-0.5" x-text="selectedAstapDetail.tahun_perolehan"></p>
                        </div>
                        <div class="bg-slate-950/60 border border-slate-800 rounded-2xl p-3">
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Kondisi Aset</span>
                            <p class="text-sm font-bold text-emerald-400 mt-0.5" x-text="selectedAstapDetail.kondisi"></p>
                        </div>
                        <div class="bg-slate-950/60 border border-slate-800 rounded-2xl p-3 col-span-2">
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Volume / Kuantitas / Luas Aset</span>
                            <p class="text-sm font-bold text-teal-300 mt-0.5 flex items-center space-x-1 font-mono">
                                <span>📏</span>
                                <span x-text="selectedAstapDetail.volume_satuan"></span>
                            </p>
                        </div>
                    </div>

                    <!-- Blok 1: Identitas & Spesifikasi Fisik -->
                    <div class="p-4 rounded-2xl bg-slate-950/50 border border-slate-800 space-y-2.5">
                        <h4 class="text-xs font-bold text-white uppercase tracking-wider flex items-center space-x-2">
                            <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                            <span>1. Spesifikasi Teknis & Legalitas</span>
                        </h4>
                        <div class="grid grid-cols-2 gap-3 pt-1 text-[11px]">
                            <div>
                                <span class="text-slate-400">Jenis Klasifikasi:</span>
                                <p class="font-semibold text-white" x-text="selectedAstapDetail.jenis_aset_nama"></p>
                            </div>
                            <div>
                                <span class="text-slate-400">Merk & Type:</span>
                                <p class="font-semibold text-white" x-text="selectedAstapDetail.merk + ' ' + (selectedAstapDetail.type !== '-' ? selectedAstapDetail.type : '')"></p>
                            </div>
                            <div>
                                <span class="text-slate-400">Ukuran / Luas / Kapasitas:</span>
                                <p class="font-semibold text-white" x-text="selectedAstapDetail.ukuran"></p>
                            </div>
                            <div>
                                <span class="text-slate-400">No. Pabrik / IMB / Sertifikat:</span>
                                <p class="font-semibold text-white font-mono" x-text="selectedAstapDetail.no_pabrik"></p>
                            </div>
                            <div>
                                <span class="text-slate-400">Bahan / Material:</span>
                                <p class="font-semibold text-white" x-text="selectedAstapDetail.bahan"></p>
                            </div>
                            <div>
                                <span class="text-slate-400">Asal Usul Sumber:</span>
                                <p class="font-semibold text-white" x-text="selectedAstapDetail.asal_usul"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Blok 2: Penganggaran & Akuntansi -->
                    <div class="p-4 rounded-2xl bg-slate-950/50 border border-slate-800 space-y-2.5">
                        <h4 class="text-xs font-bold text-white uppercase tracking-wider flex items-center space-x-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            <span>2. Penganggaran & Akuntansi (DPA)</span>
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1 text-[11px]">
                            <div>
                                <span class="text-slate-400">Program:</span>
                                <p class="font-semibold text-white" x-text="selectedAstapDetail.program_nama"></p>
                            </div>
                            <div>
                                <span class="text-slate-400">Kegiatan:</span>
                                <p class="font-semibold text-white" x-text="selectedAstapDetail.kegiatan_nama"></p>
                            </div>
                            <div>
                                <span class="text-slate-400">Sub Kegiatan:</span>
                                <p class="font-semibold text-white" x-text="selectedAstapDetail.sub_kegiatan_nama"></p>
                            </div>
                            <div>
                                <span class="text-slate-400">Rekening Belanja:</span>
                                <p class="font-semibold text-white" x-text="selectedAstapDetail.rekening_nama"></p>
                            </div>
                            <div class="sm:col-span-2 pt-1 border-t border-slate-800/80 flex items-center justify-between">
                                <span class="text-slate-400 font-semibold">Total Nilai Realisasi:</span>
                                <span class="text-base font-extrabold text-emerald-400 font-mono" x-text="selectedAstapDetail.jumlah_realisasi"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Blok 3: Dokumen Pengadaan & Kontrak -->
                    <div class="p-4 rounded-2xl bg-slate-950/50 border border-slate-800 space-y-2.5">
                        <h4 class="text-xs font-bold text-white uppercase tracking-wider flex items-center space-x-2">
                            <span class="w-2 h-2 rounded-full bg-purple-400"></span>
                            <span>3. Dokumen Pengadaan & Bukti Transaksi</span>
                        </h4>
                        <div class="grid grid-cols-2 gap-3 pt-1 text-[11px]">
                            <div>
                                <span class="text-slate-400">No. Surat Pesanan:</span>
                                <p class="font-mono font-semibold text-purple-300" x-text="selectedAstapDetail.surat_pesanan_nomor"></p>
                            </div>
                            <div>
                                <span class="text-slate-400">No. SPK / Kontrak:</span>
                                <p class="font-mono font-semibold text-purple-300" x-text="selectedAstapDetail.spk_nomor"></p>
                            </div>
                            <div>
                                <span class="text-slate-400">No. Kwitansi:</span>
                                <p class="font-mono font-semibold text-slate-300" x-text="selectedAstapDetail.kwitansi_nomor"></p>
                            </div>
                            <div>
                                <span class="text-slate-400">No. Faktur / Invoice:</span>
                                <p class="font-mono font-semibold text-slate-300" x-text="selectedAstapDetail.faktur_nomor"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Keterangan Tambahan -->
                    <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 text-[11px] text-slate-300">
                        <span class="text-slate-500 font-semibold block mb-0.5">Catatan / Keterangan:</span>
                        <span x-text="selectedAstapDetail.keterangan"></span>
                    </div>

                </div>

                <!-- Modal Detail Footer -->
                <div class="p-4 sm:p-6 border-t border-slate-800 bg-slate-950/60 flex items-center justify-between">
                    <button type="button" @click="downloadQrCode(selectedAstapDetail.kode_barang, selectedAstapDetail.nama_barang)"
                        class="px-4 py-2 rounded-xl bg-teal-500/15 hover:bg-teal-500/25 text-teal-300 border border-teal-500/30 font-bold text-xs transition-all flex items-center space-x-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                        <span>Cetak Label & QR Code</span>
                    </button>

                    <button type="button" @click="showDetailModal = false"
                        class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all">
                        Tutup
                    </button>
                </div>

            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- FRONTEND MODAL WIZARD: MULTI-STEP PENAMBAHAN DATA ASTAP                   -->
        <!-- ========================================================================= -->
        <div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4 sm:p-6">
            <div @click.away="resetModal()" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-3xl w-full shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                
                <!-- Modal Header & Step Indicator -->
                <div class="p-6 border-b border-slate-800 bg-slate-950/60">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-2">
                            <span class="p-2 rounded-xl bg-emerald-500/10 text-emerald-400 font-bold">📝</span>
                            <div>
                                <h3 class="text-base font-extrabold text-white">Form Penambahan Data ASTAP</h3>
                                <p class="text-[11px] text-slate-400">Pengisian terstruktur sesuai format KIB & Penganggaran BMD</p>
                            </div>
                        </div>
                        <button type="button" @click="resetModal()" class="text-slate-500 hover:text-white text-xl font-bold">&times;</button>
                    </div>

                    <!-- Step Wizard Indicator (1 to 4) -->
                    <div class="grid grid-cols-4 gap-2">
                        <div class="flex flex-col items-center">
                            <div class="w-full h-1.5 rounded-full mb-1.5 transition-all"
                                 :class="currentStep >= 1 ? 'bg-emerald-500 shadow-sm shadow-emerald-500/50' : 'bg-slate-800'"></div>
                            <span class="text-[10px] font-bold" :class="currentStep === 1 ? 'text-emerald-400' : 'text-slate-500'">1. Jenis Aset (KIB)</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-full h-1.5 rounded-full mb-1.5 transition-all"
                                 :class="currentStep >= 2 ? 'bg-emerald-500 shadow-sm shadow-emerald-500/50' : 'bg-slate-800'"></div>
                            <span class="text-[10px] font-bold" :class="currentStep === 2 ? 'text-emerald-400' : 'text-slate-500'">2. Penganggaran</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-full h-1.5 rounded-full mb-1.5 transition-all"
                                 :class="currentStep >= 3 ? 'bg-emerald-500 shadow-sm shadow-emerald-500/50' : 'bg-slate-800'"></div>
                            <span class="text-[10px] font-bold" :class="currentStep === 3 ? 'text-emerald-400' : 'text-slate-500'">3. Dokumen Beli</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-full h-1.5 rounded-full mb-1.5 transition-all"
                                 :class="currentStep >= 4 ? 'bg-emerald-500 shadow-sm shadow-emerald-500/50' : 'bg-slate-800'"></div>
                            <span class="text-[10px] font-bold" :class="currentStep === 4 ? 'text-emerald-400' : 'text-slate-500'">4. Spesifikasi KIB</span>
                        </div>
                    </div>
                </div>

                <!-- Modal Body (Scrollable Multi-Step Container) -->
                <div class="p-6 overflow-y-auto flex-1 text-xs space-y-5">

                    <!-- STEP 1: PILIH JENIS UTAMA (CARD) & SUB RINCIAN (FILTER) -->
                    <div x-show="currentStep === 1" class="space-y-5">
                        <div>
                            <h4 class="text-sm font-bold text-white mb-1">Langkah 1: Pilih Kategori Jenis Utama Aset</h4>
                            <p class="text-slate-400 text-xs">Pilih salah satu kartu kategori KIB di bawah ini:</p>
                        </div>

                        <!-- Card Selection Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <div @click="selectCategory('KIB A', '1.3.1', 'TANAH')" 
                                 class="p-4 rounded-2xl border cursor-pointer transition-all flex flex-col justify-between"
                                 :class="formData.category === 'KIB A' ? 'bg-emerald-500/10 border-emerald-500 ring-2 ring-emerald-500/30' : 'bg-slate-950/60 border-slate-800 hover:border-slate-700'">
                                <div class="text-2xl mb-2">🌾</div>
                                <div>
                                    <span class="text-[10px] font-bold text-emerald-400">1.3.1</span>
                                    <h5 class="text-xs font-bold text-white">KIB A - Tanah</h5>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Lahan & tanah bangunan</p>
                                </div>
                            </div>

                            <div @click="selectCategory('KIB B', '1.3.2', 'PERALATAN DAN MESIN')" 
                                 class="p-4 rounded-2xl border cursor-pointer transition-all flex flex-col justify-between"
                                 :class="formData.category === 'KIB B' ? 'bg-cyan-500/10 border-cyan-500 ring-2 ring-cyan-500/30' : 'bg-slate-950/60 border-slate-800 hover:border-slate-700'">
                                <div class="text-2xl mb-2">🔬</div>
                                <div>
                                    <span class="text-[10px] font-bold text-cyan-400">1.3.2</span>
                                    <h5 class="text-xs font-bold text-white">KIB B - Alat & Mesin</h5>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Alat medis & mesin RS</p>
                                </div>
                            </div>

                            <div @click="selectCategory('KIB C', '1.3.3', 'GEDUNG DAN BANGUNAN')" 
                                 class="p-4 rounded-2xl border cursor-pointer transition-all flex flex-col justify-between"
                                 :class="formData.category === 'KIB C' ? 'bg-purple-500/10 border-purple-500 ring-2 ring-purple-500/30' : 'bg-slate-950/60 border-slate-800 hover:border-slate-700'">
                                <div class="text-2xl mb-2">🏢</div>
                                <div>
                                    <span class="text-[10px] font-bold text-purple-400">1.3.3</span>
                                    <h5 class="text-xs font-bold text-white">KIB C - Gedung</h5>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Paviliun & poliklinik</p>
                                </div>
                            </div>

                            <div @click="selectCategory('KIB D', '1.3.4', 'JALAN, IRIGASI DAN JARINGAN')" 
                                 class="p-4 rounded-2xl border cursor-pointer transition-all flex flex-col justify-between"
                                 :class="formData.category === 'KIB D' ? 'bg-teal-500/10 border-teal-500 ring-2 ring-teal-500/30' : 'bg-slate-950/60 border-slate-800 hover:border-slate-700'">
                                <div class="text-2xl mb-2">🚰</div>
                                <div>
                                    <span class="text-[10px] font-bold text-teal-400">1.3.4</span>
                                    <h5 class="text-xs font-bold text-white">KIB D - Jaringan</h5>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Pipa oksigen & instalasi</p>
                                </div>
                            </div>

                            <div @click="selectCategory('KIB E', '1.3.5', 'ASET TETAP LAINNYA')" 
                                 class="p-4 rounded-2xl border cursor-pointer transition-all flex flex-col justify-between"
                                 :class="formData.category === 'KIB E' ? 'bg-amber-500/10 border-amber-500 ring-2 ring-amber-500/30' : 'bg-slate-950/60 border-slate-800 hover:border-slate-700'">
                                <div class="text-2xl mb-2">📦</div>
                                <div>
                                    <span class="text-[10px] font-bold text-amber-400">1.3.5</span>
                                    <h5 class="text-xs font-bold text-white">KIB E - Aset Lainnya</h5>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Buku & literatur medis</p>
                                </div>
                            </div>

                            <div @click="selectCategory('KIB F', '1.3.5.07', 'ASET TETAP DALAM RENOVASI')" 
                                 class="p-4 rounded-2xl border cursor-pointer transition-all flex flex-col justify-between"
                                 :class="formData.category === 'KIB F' ? 'bg-rose-500/10 border-rose-500 ring-2 ring-rose-500/30' : 'bg-slate-950/60 border-slate-800 hover:border-slate-700'">
                                <div class="text-2xl mb-2">🏗️</div>
                                <div>
                                    <span class="text-[10px] font-bold text-rose-400">1.3.5.07</span>
                                    <h5 class="text-xs font-bold text-white">KIB F - Renovasi</h5>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Dalam renovasi / KDP</p>
                                </div>
                            </div>
                        </div>

                        <!-- Sub Rincian & Kode 108 Filter Selection -->
                        <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-3">
                            <h5 class="text-xs font-bold text-white flex items-center space-x-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                <span>Filter Sub Rincian & Kode 108 BMD</span>
                            </h5>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-slate-400 text-[11px] mb-1">Sub Rincian Objek</label>
                                    <select x-model="formData.sub_rincian_nama" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-white">
                                        <template x-if="formData.category === 'KIB A'">
                                            <option value="TANAH BANGUNAN PERUMAHAN/G.TEMPAT TINGGAL">1.3.1.01.01.01 - TANAH BANGUNAN RUMAH/GEDUNG TINGGAL</option>
                                        </template>
                                        <template x-if="formData.category === 'KIB A'">
                                            <option value="TANAH UNTUK BANGUNAN GED.PERDAGANGAN/PERUSAHAAN">1.3.1.01.01.02 - TANAH GEDUNG APOTIK/RSUD</option>
                                        </template>
                                        <template x-if="formData.category === 'KIB B'">
                                            <option value="ALAT KEDOKTERAN KESEHATAN MATRA">1.3.2.02.01.01 - ALAT KEDOKTERAN UMUM & RADIOLOGI</option>
                                        </template>
                                        <template x-if="formData.category === 'KIB C'">
                                            <option value="BANGUNAN GEDUNG TEMPAT KERJA">1.3.3.01.01.01 - BANGUNAN GEDUNG RUMAH SAKIT</option>
                                        </template>
                                        <template x-if="formData.category === 'KIB D'">
                                            <option value="INSTALASI LISTRIK & OKSIGEN MEDIS">1.3.4.03.01.01 - JARINGAN PIPA OKSIGEN MEDIS</option>
                                        </template>
                                        <template x-if="formData.category === 'KIB E'">
                                            <option value="BUKU DAN PERPUSTAKAAN MEDIS">1.3.5.01.01.01 - BUKU LITERATUR KEDOKTERAN</option>
                                        </template>
                                        <template x-if="formData.category === 'KIB F'">
                                            <option value="ASET TETAP DALAM RENOVASI">1.3.5.07.01.01 - GEDUNG/BANGUNAN DALAM RENOVASI</option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[11px] mb-1">Kode 108 Barang Spesifik</label>
                                    <input type="text" x-model="formData.kode_barang" placeholder="Contoh: 1.3.1.01.01.02.013" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-white font-mono">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: BLOK PENGANGGARAN & AKUNTANSI -->
                    <div x-show="currentStep === 2" class="space-y-4">
                        <div>
                            <h4 class="text-sm font-bold text-white mb-1">Langkah 2: 📑 Blok Penganggaran & Akuntansi</h4>
                            <p class="text-slate-400 text-xs">Isikan rincian sumber program, kegiatan, rekening belanja, dan nilai anggaran.</p>
                        </div>

                        <!-- Program -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-slate-400 text-[11px] mb-1">Kode Program</label>
                                <input type="text" x-model="formData.program_kode" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-mono">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-slate-400 text-[11px] mb-1">Nama Program</label>
                                <input type="text" x-model="formData.program_nama" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                            </div>
                        </div>

                        <!-- Kegiatan -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-slate-400 text-[11px] mb-1">Kode Kegiatan</label>
                                <input type="text" x-model="formData.kegiatan_kode" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-mono">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-slate-400 text-[11px] mb-1">Nama Kegiatan</label>
                                <input type="text" x-model="formData.kegiatan_nama" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                            </div>
                        </div>

                        <!-- Sub Kegiatan -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-slate-400 text-[11px] mb-1">Kode Sub Kegiatan</label>
                                <input type="text" x-model="formData.sub_kegiatan_kode" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-mono">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-slate-400 text-[11px] mb-1">Nama Sub Kegiatan</label>
                                <input type="text" x-model="formData.sub_kegiatan_nama" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                            </div>
                        </div>

                        <!-- Rekening Belanja -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-slate-400 text-[11px] mb-1">Kode Rekening</label>
                                <input type="text" x-model="formData.rekening_kode" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-mono">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-slate-400 text-[11px] mb-1">Nama / Uraian Rekening Belanja</label>
                                <input type="text" x-model="formData.rekening_nama" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                            </div>
                        </div>

                        <!-- Jumlah Anggaran & Realisasi -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                            <div>
                                <label class="block text-slate-400 text-[11px] mb-1">Jumlah Anggaran DPA (Rp)</label>
                                <input type="text" x-model="formData.jumlah_anggaran" placeholder="Contoh: 1.500.000.000" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-mono">
                            </div>
                            <div>
                                <label class="block text-emerald-400 font-semibold text-[11px] mb-1">Jumlah Realisasi Anggaran (Rp)</label>
                                <input type="text" x-model="formData.jumlah_realisasi" placeholder="Contoh: 1.450.000.000" class="w-full bg-slate-950 border border-emerald-500/40 rounded-xl px-3 py-2 text-emerald-300 font-bold font-mono">
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3: BLOK DOKUMEN PEMBELIAN & PENGADAAN -->
                    <div x-show="currentStep === 3" class="space-y-4">
                        <div>
                            <h4 class="text-sm font-bold text-white mb-1">Langkah 3: 🧾 Blok Dokumen Pembelian & Pengadaan</h4>
                            <p class="text-slate-400 text-xs">Lengkapi nomor dan tanggal surat pesanan, kontrak SPK, kwitansi, serta invoice.</p>
                        </div>

                        <!-- SPK / Kontrak -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-slate-400 text-[11px] mb-1">Nomor SPK / Kontrak</label>
                                <input type="text" x-model="formData.spk_nomor" placeholder="045/SPK-MED/2026" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[11px] mb-1">Tanggal SPK</label>
                                <input type="date" x-model="formData.spk_tanggal" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                            </div>
                        </div>

                        <!-- Surat Pesanan -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-slate-400 text-[11px] mb-1">Nomor Surat Pesanan</label>
                                <input type="text" x-model="formData.surat_pesanan_nomor" placeholder="019/BN.BA/VI/2025" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[11px] mb-1">Tanggal Surat Pesanan</label>
                                <input type="date" x-model="formData.surat_pesanan_tanggal" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                            </div>
                        </div>

                        <!-- Kwitansi Pembayaran -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-slate-400 text-[11px] mb-1">Nomor Kwitansi</label>
                                <input type="text" x-model="formData.kwitansi_nomor" placeholder="KW-092/RSUD/2026" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[11px] mb-1">Tanggal Kwitansi</label>
                                <input type="date" x-model="formData.kwitansi_tanggal" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                            </div>
                        </div>

                        <!-- Faktur / Invoice -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-slate-400 text-[11px] mb-1">Nomor Faktur / Invoice</label>
                                <input type="text" x-model="formData.faktur_nomor" placeholder="INV-2026/088" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[11px] mb-1">Tanggal Faktur</label>
                                <input type="date" x-model="formData.faktur_tanggal" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                            </div>
                        </div>
                    </div>

                    <!-- STEP 4: BLOK SPESIFIKASI TEKNIS KIB -->
                    <div x-show="currentStep === 4" class="space-y-4">
                        <div>
                            <h4 class="text-sm font-bold text-white mb-1">Langkah 4: 🌾 Blok Spesifikasi Teknis Barang</h4>
                            <p class="text-slate-400 text-xs">Lengkapi identitas fisik, lokasi penempatan, dan legalitas aset (<span class="text-emerald-400 font-bold" x-text="formData.category"></span>).</p>
                        </div>

                        <!-- Nama Barang & Lokasi Penempatan -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-slate-300 font-semibold mb-1">Nama Barang / Aset</label>
                                <input type="text" x-model="formData.nama_barang" placeholder="Nama barang aset..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                            </div>
                            <div>
                                <label class="block text-teal-400 font-semibold mb-1">Lokasi Penempatan Unit</label>
                                <input type="text" x-model="formData.lokasi_penempatan" placeholder="Contoh: Paviliun Graha Amukti / IGD" class="w-full bg-slate-950 border border-teal-500/40 rounded-xl px-3.5 py-2.5 text-teal-200 font-semibold">
                            </div>
                        </div>

                        <!-- SPESIFIKASI KHUSUS JIKA KIB A (TANAH) -->
                        <template x-if="formData.category === 'KIB A'">
                            <div class="space-y-3 p-4 rounded-2xl bg-amber-500/5 border border-amber-500/20">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-slate-400 text-[11px] mb-1">Luas Tanah (M²)</label>
                                        <input type="text" x-model="formData.luas_tanah" placeholder="35.400 m²" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[11px] mb-1">Tahun Pengadaan</label>
                                        <input type="text" x-model="formData.tahun_pengadaan" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[11px] mb-1">Hak / Status Tanah</label>
                                        <select x-model="formData.hak_tanah" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                                            <option>Hak Pakai</option>
                                            <option>Hak Milik</option>
                                            <option>Hak Pengelolaan</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-slate-400 text-[11px] mb-1">Nomor Sertifikat</label>
                                        <input type="text" x-model="formData.sertifikat_nomor" placeholder="HP-108/RSUD/1984" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[11px] mb-1">Tanggal Sertifikat</label>
                                        <input type="date" x-model="formData.sertifikat_tanggal" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-slate-400 text-[11px] mb-1">Letak / Alamat / Lokasi Tanah</label>
                                    <input type="text" x-model="formData.letak_lokasi" placeholder="Jl. Kapten Pierre Tendean No. 3, Bondowoso" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-slate-400 text-[11px] mb-1">Penggunaan Tanah</label>
                                        <input type="text" x-model="formData.penggunaan" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[11px] mb-1">Asal Usul Perolehan</label>
                                        <select x-model="formData.asal_usul" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                                            <option>APBD Kabupaten</option>
                                            <option>BLUD RSUD</option>
                                            <option>Hibah Pemerintah</option>
                                            <option>Pembelian</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- SPESIFIKASI KHUSUS JIKA KIB B/C/D/E/F -->
                        <template x-if="formData.category !== 'KIB A'">
                            <div class="space-y-3 p-4 rounded-2xl bg-cyan-500/5 border border-cyan-500/20">
                                <div class="grid grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-slate-400 text-[11px] mb-1">Merk</label>
                                        <input type="text" x-model="formData.merk" placeholder="Siemens / Terumo..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[11px] mb-1">Type / Model</label>
                                        <input type="text" x-model="formData.type" placeholder="128-Slice / dsb" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[11px] mb-1">Bahan</label>
                                        <input type="text" x-model="formData.bahan" placeholder="Bahan..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-slate-400 text-[11px] mb-1">Ukuran / Spesifikasi</label>
                                        <input type="text" x-model="formData.ukuran" placeholder="Ukuran / kapasitas..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[11px] mb-1">No. Pabrik / No. Rangka</label>
                                        <input type="text" x-model="formData.no_pabrik" placeholder="SN-XXXXX" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Kondisi & Keterangan -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-slate-400 text-[11px] mb-1">Kondisi Barang</label>
                                <select x-model="formData.kondisi" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                                    <option value="Baik">Baik (B)</option>
                                    <option value="Rusak Ringan">Rusak Ringan (RR)</option>
                                    <option value="Rusak Berat">Rusak Berat (RB)</option>
                                    <option value="Dalam Renovasi">Dalam Renovasi</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[11px] mb-1">Keterangan Tambahan</label>
                                <input type="text" x-model="formData.keterangan" placeholder="Keterangan..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white">
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Modal Footer (Step Navigation Buttons) -->
                <div class="p-4 sm:p-6 border-t border-slate-800 bg-slate-950/60 flex items-center justify-between">
                    <div>
                        <button type="button" x-show="currentStep > 1" @click="currentStep--"
                            class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-all flex items-center space-x-1.5">
                            <span>&larr; Kembali</span>
                        </button>
                    </div>

                    <div class="flex items-center space-x-2">
                        <button type="button" @click="resetModal()"
                            class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-all">
                            Batal
                        </button>

                        <!-- Next Button (Step 1-3) -->
                        <button type="button" x-show="currentStep < totalSteps" @click="currentStep++"
                            class="px-5 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-1.5">
                            <span>Lanjut Langkah Berikutnya &rarr;</span>
                        </button>

                        <!-- Submit Button (Step 4) -->
                        <button type="button" x-show="currentStep === totalSteps" @click="alert('✅ Data ASTAP ' + formData.category + ' berhasil disimpan!'); resetModal()"
                            class="px-5 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-emerald-500/30 transition-all flex items-center space-x-1.5">
                            <span>✓ Simpan Data ASTAP Lengkap</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- FRONTEND MODAL: UBAH ASTAP -->
        <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
            <div @click.away="showEditModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-xl w-full p-6 shadow-2xl overflow-y-auto max-h-[90vh]">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                    <h3 class="text-base font-bold text-white">✏️ Ubah Data ASTAP</h3>
                    <button type="button" @click="showEditModal = false" class="text-slate-500 hover:text-white">&times;</button>
                </div>
                <form @submit.prevent="showEditModal = false" class="space-y-4 text-xs">
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Nama Barang</label>
                        <input type="text" x-model="selectedAstap ? selectedAstap.nama_barang : ''" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Lokasi Penempatan Unit</label>
                        <input type="text" x-model="selectedAstap ? selectedAstap.lokasi_penempatan : ''" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-slate-300 font-semibold mb-1">Tahun Perolehan</label>
                            <input type="text" x-model="selectedAstap ? selectedAstap.tahun_perolehan : ''" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                        </div>
                        <div>
                            <label class="block text-slate-300 font-semibold mb-1">Kondisi</label>
                            <select x-model="selectedAstap ? selectedAstap.kondisi : 'Baik'" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                                <option value="Baik">Baik (B)</option>
                                <option value="Rusak Ringan">Rusak Ringan (RR)</option>
                                <option value="Rusak Berat">Rusak Berat (RB)</option>
                                <option value="Dalam Renovasi">Dalam Renovasi</option>
                            </select>
                        </div>
                    </div>
                    <div class="pt-4 flex items-center justify-end space-x-2">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-cyan-500 text-slate-950 font-bold">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layout>
