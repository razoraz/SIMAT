<x-layout title="Berita Acara (BAST) - SIMAT-RK">
    @section('page-title', 'Berita Acara (BAST)')
    @section('breadcrumb', 'Master Utama / Berita Acara (BAST)')

    <div x-data="{
        // Tab Navigasi Aktif: 'triwulan' (Penambahan ASTAP) atau 'distribusi' (Distribusi ke Unit/Paviliun)
        activeTab: 'triwulan',
        
        // =========================================================================
        // DATA TAB 1: BAST PENAMBAHAN DATA ASTAP BERDASARKAN TRIWULAN
        // =========================================================================
        selectedTahun: '2026',
        selectedTriwulanKey: 'TW2',
        showPrintTriwulanModal: false,
        showEditTriwulanForm: false,
        showEditDistribusiForm: false,
        searchBarangTriwulan: '',

        triwulanData: {
            'TW1': {
                key: 'TW1',
                nomor_surat: '000.2.3.2/112/430.10.7/2026',
                hari_tanggal: 'Selasa tanggal 31 Maret 2026',
                triwulan_nama: 'Triwulan I (Januari - Maret) Tahun 2026',
                lokasi: 'Rumah Sakit Umum Daerah dr.H.Koesnandi Kabupaten Bondowoso',
                pihak1_nama: 'dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR.',
                pihak1_nip: '19771002 200604 1 006',
                pihak1_nip_ttd: '19771126 199901 1 001',
                pihak1_jabatan: 'Pejabat Pembuat Komitmen (PPK) / Penerima Hasil Pengadaan RSUD dr.H.Koesnandi',
                pihak2_nama: 'BUDI HARTONO,S.Sos',
                pihak2_nip: '19760229 200801 1 010',
                pihak2_jabatan: 'Pengurus Barang Aset Pada RSUD dr.H.Koesnandi Kabupaten Bondowoso',
                direktur_nama: 'dr. DIAN ARISANDI, M.Kes',
                direktur_nip: '19730514 200212 2 003',
                pihak2_signed: true,
                pihak2_tgl_ttd: '31/03/2026 15:40 WIB',
                pihak2_qr_hash: 'BSRE-KOESNANDI-BAST-TW1-2026-0914',
                
                // Rekapitulasi 8 Kategori Aset Triwulan I
                rekapItems: [
                    { no: '1.', nama: 'Tanah', qty: 0, nilai: 0 },
                    { no: '2.', nama: 'Peralatan Dan Mesin', qty: 120, nilai: 985500000 },
                    { no: '3.', nama: 'Gedung Dan Bangunan', qty: 0, nilai: 0 },
                    { no: '4.', nama: 'Jalan, Irigasi Dan Jaringan', qty: 0, nilai: 0 },
                    { no: '5.', nama: 'Aset Tetap Lainnya', qty: 0, nilai: 0 },
                    { no: '6.', nama: 'Kontruksi Dalam Pengerjaan (KDP)', qty: 0, nilai: 0 },
                    { no: '7.', nama: 'Aset Tidak Berwujud (ATB)', qty: 1, nilai: 135000000 },
                    { no: '8.', nama: 'Exstra Comtable', qty: 3, nilai: 4500000 }
                ],

                // Daftar Rincian Barang yang Diadakan Triwulan I
                detailBarang: [
                    { no: 1, tanggal_sp2d: '15/02/2026', nomor_spk: '027/015/SPK-MED/2026', rekening: '5.2.02.01.01.0004', kode_108: '1.3.2.02.01.01.012', nama_barang: 'Infusion Pump Terumo TE-171', spesifikasi: 'Flow rate 0.1-1200 mL/h, Battery Backup 4h', penyedia: 'PT Medika Farma Pratama', volume: 10, satuan: 'Unit', nilai_realisasi: 185000000 },
                    { no: 2, tanggal_sp2d: '20/02/2026', nomor_spk: '027/018/SPK-MED/2026', rekening: '5.2.02.01.01.0004', kode_108: '1.3.2.02.01.01.014', nama_barang: 'Syringe Pump Terumo TE-331', spesifikasi: 'Auto Syringe Size Detection 10/20/50mL', penyedia: 'PT Medika Farma Pratama', volume: 10, satuan: 'Unit', nilai_realisasi: 175000000 },
                    { no: 3, tanggal_sp2d: '10/03/2026', nomor_spk: '027/022/SPK-RAD/2026', rekening: '5.2.02.01.01.0004', kode_108: '1.3.2.02.01.02.008', nama_barang: 'USG 4D Mindray DC-30 Color Doppler', spesifikasi: 'Convex & Transvaginal Probe, LCD 17 Inch', penyedia: 'CV Tri Bintang Medika', volume: 1, satuan: 'Unit', nilai_realisasi: 450000000 },
                    { no: 4, tanggal_sp2d: '18/03/2026', nomor_spk: '027/029/SPK-IT/2026', rekening: '5.2.02.05.01.0001', kode_108: '1.3.2.05.02.01.003', nama_barang: 'Server SIMRS Rackmount Dell PowerEdge', spesifikasi: 'Intel Xeon Silver 16 Core, 64GB ECC RAM, 4TB SSD', penyedia: 'CV Multi Media Solusindo', volume: 1, satuan: 'Unit', nilai_realisasi: 175500000 },
                    { no: 5, tanggal_sp2d: '25/03/2026', nomor_spk: '027/031/SPK-ATB/2026', rekening: '5.2.05.01.01.0001', kode_108: '1.5.3.01.01.01.001', nama_barang: 'Software SIMRS Modul PACs Radiologi', spesifikasi: 'Lisensi Institusi Unlimited Client & DICOM Viewer', penyedia: 'PT Integra Medika Pratama', volume: 1, satuan: 'Lisensi', nilai_realisasi: 135000000 }
                ]
            },

            'TW2': {
                key: 'TW2',
                nomor_surat: '000.2.3.2/224/430.10.7/2026',
                hari_tanggal: 'Selasa tanggal 30 Juni 2026',
                triwulan_nama: 'Triwulan II (April - Juni) Tahun 2026',
                lokasi: 'Rumah Sakit Umum Daerah dr.H.Koesnandi Kabupaten Bondowoso',
                pihak1_nama: 'dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR.',
                pihak1_nip: '19771002 200604 1 006',
                pihak1_nip_ttd: '19771126 199901 1 001',
                pihak1_jabatan: 'Pejabat Pembuat Komitmen (PPK) / Penerima Hasil Pengadaan RSUD dr.H.Koesnandi',
                pihak2_nama: 'BUDI HARTONO,S.Sos',
                pihak2_nip: '19760229 200801 1 010',
                pihak2_jabatan: 'Pengurus Barang Aset Pada RSUD dr.H.Koesnandi Kabupaten Bondowoso',
                direktur_nama: 'dr. DIAN ARISANDI, M.Kes',
                direktur_nip: '19730514 200212 2 003',
                pihak2_signed: true,
                pihak2_tgl_ttd: '30/06/2026 14:32 WIB',
                pihak2_qr_hash: 'BSRE-KOESNANDI-BAST-TW2-2026-0887',

                // Rekapitulasi 8 Kategori Aset Triwulan II
                rekapItems: [
                    { no: '1.', nama: 'Tanah', qty: 0, nilai: 0 },
                    { no: '2.', nama: 'Peralatan Dan Mesin', qty: 477, nilai: 3854986225 },
                    { no: '3.', nama: 'Gedung Dan Bangunan', qty: 0, nilai: 0 },
                    { no: '4.', nama: 'Jalan, Irigasi Dan Jaringan', qty: 0, nilai: 0 },
                    { no: '5.', nama: 'Aset Tetap Lainnya', qty: 0, nilai: 0 },
                    { no: '6.', nama: 'Kontruksi Dalam Pengerjaan (KDP)', qty: 0, nilai: 0 },
                    { no: '7.', nama: 'Aset Tidak Berwujud (ATB)', qty: 1, nilai: 777000000 },
                    { no: '8.', nama: 'Exstra Comtable', qty: 202, nilai: 33302220 }
                ],

                // Daftar Rincian Barang yang Diadakan Triwulan II
                detailBarang: [
                    { no: 1, tanggal_sp2d: '15/04/2026', nomor_spk: '027/044/SPK-RAWAT/2026', rekening: '5.2.02.01.01.0004', kode_108: '1.3.2.02.01.01.005', nama_barang: 'Bed Patient Electric 3 Crank Acare', spesifikasi: 'Model CPR Electric, Central Lock Caster, Matras Anti Decubitus', penyedia: 'PT Surya Alkesindo Mandiri', volume: 30, satuan: 'Unit', nilai_realisasi: 540000000 },
                    { no: 2, tanggal_sp2d: '28/04/2026', nomor_spk: '027/048/SPK-MED/2026', rekening: '5.2.02.01.01.0004', kode_108: '1.3.2.02.01.02.010', nama_barang: 'Patient Monitor 6 Parameter Mindray ePM 12', spesifikasi: 'ECG, NIBP, SpO2, Resp, 2-Temp, IBP Ready, Screen 12.1 Inch', penyedia: 'CV Tri Bintang Medika', volume: 15, satuan: 'Unit', nilai_realisasi: 480000000 },
                    { no: 3, tanggal_sp2d: '14/05/2026', nomor_spk: '027/050/SPK-RAD/2026', rekening: '5.2.02.01.01.0004', kode_108: '1.3.2.02.01.02.001', nama_barang: 'CT-Scan 128 Slice Siemens SOMATOM go.Top', spesifikasi: 'Stellar Detector 128 Slice, Low Dose AI Reconstruction', penyedia: 'PT Siemens Healthineers Indonesia', volume: 1, satuan: 'Unit', nilai_realisasi: 2450000000 },
                    { no: 4, tanggal_sp2d: '02/06/2026', nomor_spk: '027/055/SPK-IPSRS/2026', rekening: '5.2.02.03.01.0002', kode_108: '1.3.2.03.01.02.004', nama_barang: 'Submersible Pump Franklin 7.5 HP Sentral', spesifikasi: 'Head Max 120m, 3 Phase 380V, Stainless Steel Impeller', penyedia: 'CV Mitra Teknik Mandiri', volume: 2, satuan: 'Unit', nilai_realisasi: 74986225 },
                    { no: 5, tanggal_sp2d: '16/06/2026', nomor_spk: '027/058/SPK-IT/2026', rekening: '5.2.02.05.01.0001', kode_108: '1.3.2.05.02.06.001', nama_barang: 'Laptop Operasional Asus ExpertBook B1', spesifikasi: 'Core i7-1355U, 16GB RAM, 512GB SSD, Win 11 Pro', penyedia: 'CV Multi Media Solusindo', volume: 15, satuan: 'Unit', nilai_realisasi: 310000000 },
                    { no: 6, tanggal_sp2d: '26/06/2026', nomor_spk: '027/062/SPK-ATB/2026', rekening: '5.2.05.01.01.0001', kode_108: '1.5.3.01.01.01.001', nama_barang: 'Software Bridging Antrean Online BPJS & Rekam Medis (EMR)', spesifikasi: 'Lisensi Enterprise SATUSEHAT & BPJS VClaim V.2', penyedia: 'PT Global Health Solusindo', volume: 1, satuan: 'Lisensi', nilai_realisasi: 777000000 }
                ]
            },

            'TW3': {
                key: 'TW3',
                nomor_surat: '000.2.3.2/318/430.10.7/2026',
                hari_tanggal: 'Rabu tanggal 30 September 2026',
                triwulan_nama: 'Triwulan III (Juli - September) Tahun 2026',
                lokasi: 'Rumah Sakit Umum Daerah dr.H.Koesnandi Kabupaten Bondowoso',
                pihak1_nama: 'dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR.',
                pihak1_nip: '19771002 200604 1 006',
                pihak1_nip_ttd: '19771126 199901 1 001',
                pihak1_jabatan: 'Pejabat Pembuat Komitmen (PPK) / Penerima Hasil Pengadaan RSUD dr.H.Koesnandi',
                pihak2_nama: 'BUDI HARTONO,S.Sos',
                pihak2_nip: '19760229 200801 1 010',
                pihak2_jabatan: 'Pengurus Barang Aset Pada RSUD dr.H.Koesnandi Kabupaten Bondowoso',
                direktur_nama: 'dr. DIAN ARISANDI, M.Kes',
                direktur_nip: '19730514 200212 2 003',
                pihak2_signed: false,
                pihak2_tgl_ttd: '-',
                pihak2_qr_hash: '',

                // Rekapitulasi 8 Kategori Aset Triwulan III
                rekapItems: [
                    { no: '1.', nama: 'Tanah', qty: 0, nilai: 0 },
                    { no: '2.', nama: 'Peralatan Dan Mesin', qty: 85, nilai: 720000000 },
                    { no: '3.', nama: 'Gedung Dan Bangunan', qty: 1, nilai: 1450000000 },
                    { no: '4.', nama: 'Jalan, Irigasi Dan Jaringan', qty: 1, nilai: 380000000 },
                    { no: '5.', nama: 'Aset Tetap Lainnya', qty: 0, nilai: 0 },
                    { no: '6.', nama: 'Kontruksi Dalam Pengerjaan (KDP)', qty: 0, nilai: 0 },
                    { no: '7.', nama: 'Aset Tidak Berwujud (ATB)', qty: 0, nilai: 0 },
                    { no: '8.', nama: 'Exstra Comtable', qty: 15, nilai: 22500000 }
                ],

                // Daftar Rincian Barang yang Diadakan Triwulan III
                detailBarang: [
                    { no: 1, tanggal_sp2d: '12/07/2026', nomor_spk: '027/070/SPK-BANG/2026', rekening: '5.2.03.01.01.0001', kode_108: '1.3.3.01.01.01.002', nama_barang: 'Rehabilitasi & Renovasi Gedung Paviliun Melati Lt 2', spesifikasi: 'Pekerjaan Struktur, Arsitektur, Plafon Akustik & Cat Anti Bakteri', penyedia: 'PT Karya Bangun Persada', volume: 1, satuan: 'Paket', nilai_realisasi: 1450000000 },
                    { no: 2, tanggal_sp2d: '04/08/2026', nomor_spk: '027/074/SPK-IPAL/2026', rekening: '5.2.04.03.01.0001', kode_108: '1.3.4.03.01.01.004', nama_barang: 'Jaringan Pipa Sentral Gas Medis & IPAL RSUD', spesifikasi: 'Pipa Tembaga Standar Medis ASTM B819 & Pompa Aerasi IPAL', penyedia: 'CV Sumber Sehat Teknik', volume: 1, satuan: 'Paket', nilai_realisasi: 380000000 },
                    { no: 3, tanggal_sp2d: '21/08/2026', nomor_spk: '027/079/SPK-EMR/2026', rekening: '5.2.02.01.01.0004', kode_108: '1.3.2.02.01.03.004', nama_barang: 'Defibrillator Schiller Defigard Touch 7', spesifikasi: 'Biphasic Defibrillator, AED, Pacer & SpO2 Masimo', penyedia: 'PT Alkes Indo Medika', volume: 3, satuan: 'Unit', nilai_realisasi: 360000000 },
                    { no: 4, tanggal_sp2d: '15/09/2026', nomor_spk: '027/082/SPK-CSSD/2026', rekening: '5.2.02.01.01.0004', kode_108: '1.3.2.02.01.04.005', nama_barang: 'Autoclave Steam Sterilizer 300L CSSD', spesifikasi: 'Double Door Pass-Through, Touchscreen PLC Controller', penyedia: 'PT Medika Prima Utama', volume: 2, satuan: 'Unit', nilai_realisasi: 360000000 }
                ]
            },

            'TW4': {
                key: 'TW4',
                nomor_surat: '000.2.3.2/415/430.10.7/2026',
                hari_tanggal: 'Kamis tanggal 31 Desember 2026',
                triwulan_nama: 'Triwulan IV (Oktober - Desember) Tahun 2026',
                lokasi: 'Rumah Sakit Umum Daerah dr.H.Koesnandi Kabupaten Bondowoso',
                pihak1_nama: 'dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR.',
                pihak1_nip: '19771002 200604 1 006',
                pihak1_nip_ttd: '19771126 199901 1 001',
                pihak1_jabatan: 'Pejabat Pembuat Komitmen (PPK) / Penerima Hasil Pengadaan RSUD dr.H.Koesnandi',
                pihak2_nama: 'BUDI HARTONO,S.Sos',
                pihak2_nip: '19760229 200801 1 010',
                pihak2_jabatan: 'Pengurus Barang Aset Pada RSUD dr.H.Koesnandi Kabupaten Bondowoso',
                direktur_nama: 'dr. DIAN ARISANDI, M.Kes',
                direktur_nip: '19730514 200212 2 003',
                pihak2_signed: false,
                pihak2_tgl_ttd: '-',
                pihak2_qr_hash: '',

                // Rekapitulasi 8 Kategori Aset Triwulan IV
                rekapItems: [
                    { no: '1.', nama: 'Tanah', qty: 0, nilai: 0 },
                    { no: '2.', nama: 'Peralatan Dan Mesin', qty: 45, nilai: 410000000 },
                    { no: '3.', nama: 'Gedung Dan Bangunan', qty: 0, nilai: 0 },
                    { no: '4.', nama: 'Jalan, Irigasi Dan Jaringan', qty: 0, nilai: 0 },
                    { no: '5.', nama: 'Aset Tetap Lainnya', qty: 120, nilai: 85000000 },
                    { no: '6.', nama: 'Kontruksi Dalam Pengerjaan (KDP)', qty: 0, nilai: 0 },
                    { no: '7.', nama: 'Aset Tidak Berwujud (ATB)', qty: 0, nilai: 0 },
                    { no: '8.', nama: 'Exstra Comtable', qty: 50, nilai: 45000000 }
                ],

                detailBarang: [
                    { no: 1, tanggal_sp2d: '10/11/2026', nomor_spk: '027/095/SPK-MED/2026', rekening: '5.2.02.01.01.0004', kode_108: '1.3.2.02.01.01.008', nama_barang: 'Oxygen Concentrator 10L Portabel', spesifikasi: 'Output 93%±3%, Low Noise, Alarm Sensor', penyedia: 'PT Surya Alkesindo', volume: 10, satuan: 'Unit', nilai_realisasi: 210000000 },
                    { no: 2, tanggal_sp2d: '28/11/2026', nomor_spk: '027/102/SPK-MED/2026', rekening: '5.2.02.01.01.0004', kode_108: '1.3.2.02.01.02.015', nama_barang: 'ECG 12 Channel Bionet CardioTouch 3000', spesifikasi: '12 Lead simultaneous recording with printer', penyedia: 'CV Tri Bintang Medika', volume: 5, satuan: 'Unit', nilai_realisasi: 200000000 },
                    { no: 3, tanggal_sp2d: '15/12/2026', nomor_spk: '027/110/SPK-BUKU/2026', rekening: '5.2.02.08.01.0001', kode_108: '1.3.5.01.01.01.001', nama_barang: 'Buku Referensi Kedokteran & Jurnal Medis', spesifikasi: 'Koleksi Perpustakaan Medik RSUD Edisi Terbaru', penyedia: 'Penerbit Buku Kedokteran EGC', volume: 120, satuan: 'Eks', nilai_realisasi: 85000000 }
                ]
            }
        },

        get currentTriwulanDoc() {
            return this.triwulanData[this.selectedTriwulanKey] || this.triwulanData['TW2'];
        },

        get currentTriwulanTotalQty() {
            return this.currentTriwulanDoc.rekapItems.reduce((sum, item) => sum + Number(item.qty || 0), 0);
        },

        get currentTriwulanTotalNilai() {
            return this.currentTriwulanDoc.rekapItems.reduce((sum, item) => sum + Number(item.nilai || 0), 0);
        },

        get filteredDetailBarangTriwulan() {
            const query = (this.searchBarangTriwulan || '').toLowerCase();
            return this.currentTriwulanDoc.detailBarang.filter(b => {
                return (b.nama_barang || '').toLowerCase().includes(query) ||
                       (b.kode_108 || '').toLowerCase().includes(query) ||
                       (b.nomor_spk || '').toLowerCase().includes(query) ||
                       (b.penyedia || '').toLowerCase().includes(query);
            });
        },

        // =========================================================================
        // DATA TAB 2: BAST DISTRIBUSI BARANG KE UNIT / PAVILIUN (SUB ADMIN)
        // =========================================================================
        distribusiSearch: '',
        distribusiUnitFilter: 'all',
        showPrintDistribusiModal: false,
        selectedDistribusi: null,

        distribusiList: [
            {
                id: 1,
                nomor_bast: '032 / 034 / 430.10.7 / 2026',
                tgl_bast: 'Kamis, 13 Agustus 2026',
                hari: 'Kamis',
                tanggal_angka: '13',
                bulan: 'Agustus',
                tahun: '2026',
                tahun_anggaran: '2025',
                sk_bupati_nomor: '188.45/969/430.4.2/2024',
                sk_bupati_tanggal: '02 Januari 2025',
                unit_nama: 'Front Office (FO) & Rawat Inap',
                unit_tipe: 'Pelayanan Pasien & Rawat Inap',
                pj_nama: 'ESTU PRATIKA SARI, SST',
                pj_nip: '199409242023212002',
                pj_jabatan: 'Supervisor Front Office',
                pj_ruangan: 'FO',
                pj_jabatan_ttd: 'Kepala Ruangan FO R.Inap',
                pengurus_nama: 'BUDI HARTONO,S.Sos',
                pengurus_nip: '19760229 200801 1 010',
                pengurus_jabatan: 'Pengurus Barang',
                pengurus_ruangan: 'Gudang Perbekalan',
                status: 'Telah Diterima & Disahkan',
                keterangan_lokasi: 'BLUD-2024 u/Petugas Jaga FO R.Inap',
                signed: true,
                tgl_signed: '13/08/2026 11:30 WIB',
                qr_hash: 'BSRE-KOESNANDI-DST-FO-2026-032',
                items: [
                    { no: 1, nama_barang: 'Kasur Matras spoon', merk_type: 'Mattres Cover (Matras Spon) / Mattress Foam Adult 200x90x10', qty: 2, satuan: 'Unit', kondisi: 'Baik', keterangan: 'BLUD-2024 u/Petugas Jaga FO R.Inap' },
                    { no: 2, nama_barang: 'Bed Patient Manual 2 Crank', merk_type: 'Paramount Bed Model Standard with Side Rail', qty: 2, satuan: 'Unit', kondisi: 'Baik', keterangan: 'Ruang Rawat Observasi FO' }
                ]
            },
            {
                id: 2,
                nomor_bast: '034 / 034 / 430.10.7 / 2026',
                tgl_bast: 'Jumat, 14 Agustus 2026',
                hari: 'Jumat',
                tanggal_angka: '14',
                bulan: 'Agustus',
                tahun: '2026',
                tahun_anggaran: '2025',
                sk_bupati_nomor: '188.45/969/430.4.2/2024',
                sk_bupati_tanggal: '02 Januari 2025',
                unit_nama: 'Instalasi Gawat Darurat (IGD)',
                unit_tipe: 'Pelayanan Kedaruratan Medis',
                pj_nama: 'Ns. Hendra, S.Kep',
                pj_nip: '19880719 201202 1 002',
                pj_jabatan: 'Kepala Ruangan IGD',
                pj_ruangan: 'IGD',
                pj_jabatan_ttd: 'Kepala Ruangan IGD',
                pengurus_nama: 'BUDI HARTONO,S.Sos',
                pengurus_nip: '19760229 200801 1 010',
                pengurus_jabatan: 'Pengurus Barang',
                pengurus_ruangan: 'Gudang Perbekalan',
                status: 'Telah Diterima & Disahkan',
                keterangan_lokasi: 'Pengadaan DAK Kesehatan 2024 u/IGD Kritis',
                signed: true,
                tgl_signed: '14/08/2026 14:15 WIB',
                qr_hash: 'BSRE-KOESNANDI-DST-IGD-2026-034',
                items: [
                    { no: 1, nama_barang: 'Patient Monitor 6 Parameter', merk_type: 'Mindray ePM 12 / Display 12.1 Inch Multi-Lead ECG', qty: 4, satuan: 'Unit', kondisi: 'Baik', keterangan: 'Zona Kritis Resusitasi IGD' },
                    { no: 2, nama_barang: 'Emergency Crash Cart Trolley', merk_type: 'Stainless Steel 5 Laci + Tiang Infus & CPR Board', qty: 2, satuan: 'Unit', kondisi: 'Baik', keterangan: 'Peralatan Siaga Resusitasi IGD' }
                ]
            },
            {
                id: 3,
                nomor_bast: '037 / 034 / 430.10.7 / 2026',
                tgl_bast: 'Sabtu, 15 Agustus 2026',
                hari: 'Sabtu',
                tanggal_angka: '15',
                bulan: 'Agustus',
                tahun: '2026',
                tahun_anggaran: '2025',
                sk_bupati_nomor: '188.45/969/430.4.2/2024',
                sk_bupati_tanggal: '02 Januari 2025',
                unit_nama: 'Instalasi Pemeliharaan Sarana RS (IPSRS)',
                unit_tipe: 'Utilitas & Instalasi Sentral',
                pj_nama: 'Budi Santoso, ST',
                pj_nip: '19820510 200902 1 004',
                pj_jabatan: 'Kepala Instalasi IPSRS',
                pj_ruangan: 'IPSRS / Utility',
                pj_jabatan_ttd: 'Kepala Instalasi IPSRS',
                pengurus_nama: 'BUDI HARTONO,S.Sos',
                pengurus_nip: '19760229 200801 1 010',
                pengurus_jabatan: 'Pengurus Barang',
                pengurus_ruangan: 'Gudang Perbekalan',
                status: 'Dalam Pengiriman & Pemasangan',
                keterangan_lokasi: 'Pemasangan & Testing oleh Tim Teknisi IPSRS',
                signed: false,
                tgl_signed: '-',
                qr_hash: '',
                items: [
                    { no: 1, nama_barang: 'Submersible Pump Franklin 7.5 HP', merk_type: 'Franklin Electric 4 Inch Super Stainless 3-Phase', qty: 1, satuan: 'Unit', kondisi: 'Baik', keterangan: 'Sumur Dalam Sentral Gedung Utama' }
                ]
            },
            {
                id: 4,
                nomor_bast: '039 / 034 / 430.10.7 / 2026',
                tgl_bast: 'Minggu, 16 Agustus 2026',
                hari: 'Minggu',
                tanggal_angka: '16',
                bulan: 'Agustus',
                tahun: '2026',
                tahun_anggaran: '2025',
                sk_bupati_nomor: '188.45/969/430.4.2/2024',
                sk_bupati_tanggal: '02 Januari 2025',
                unit_nama: 'Instalasi Rekam Medis & EMR',
                unit_tipe: 'Penunjang Medis & SIMRS',
                pj_nama: 'Dewi Lestari, A.Md.RMIK',
                pj_nip: '19930814 201703 2 006',
                pj_jabatan: 'Penanggung Jawab SIMRS Rekam Medis',
                pj_ruangan: 'Rekam Medis',
                pj_jabatan_ttd: 'Kepala Instalasi Rekam Medis',
                pengurus_nama: 'BUDI HARTONO,S.Sos',
                pengurus_nip: '19760229 200801 1 010',
                pengurus_jabatan: 'Pengurus Barang',
                pengurus_ruangan: 'Gudang Perbekalan',
                status: 'Menunggu Konfirmasi Serah Terima',
                keterangan_lokasi: 'Peremajaan Unit Entri Data SIMRS & EMR',
                signed: false,
                tgl_signed: '-',
                qr_hash: '',
                items: [
                    { no: 1, nama_barang: 'Laptop Asus ExpertBook B1', merk_type: 'Core i7-1355U, 16GB DDR4, 512GB SSD, Win 11 Pro', qty: 3, satuan: 'Unit', kondisi: 'Baik', keterangan: 'Loket Pendaftaran & Coding Klaim BPJS' }
                ]
            }
        ],

        get filteredDistribusiList() {
            const query = (this.distribusiSearch || '').toLowerCase();
            return this.distribusiList.filter(d => {
                const matchQuery = (d.nomor_bast || '').toLowerCase().includes(query) ||
                                   (d.unit_nama || '').toLowerCase().includes(query) ||
                                   (d.pj_nama || '').toLowerCase().includes(query) ||
                                   (d.keterangan_lokasi || '').toLowerCase().includes(query);
                const matchUnit = this.distribusiUnitFilter === 'all' || d.unit_nama === this.distribusiUnitFilter;
                return matchQuery && matchUnit;
            });
        },

        // Helper Format Rupiah
        formatRupiah(val) {
            return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(val || 0);
        },

        // Helper Format Angka
        formatNumber(val) {
            return new Intl.NumberFormat('id-ID').format(val || 0);
        },

        // Buka Modal Cetak BAST Triwulan
        openPrintTriwulan(twKey) {
            if (twKey) this.selectedTriwulanKey = twKey;
            this.showPrintTriwulanModal = true;
        },

        // Buka Modal Cetak BAST Distribusi
        openPrintDistribusi(item) {
            this.selectedDistribusi = item ? { ...item } : this.distribusiList[0];
            this.showPrintDistribusiModal = true;
        },

        // Tandatangani BAST Distribusi
        signDistribusi() {
            if (this.selectedDistribusi) {
                this.selectedDistribusi.signed = true;
                const now = new Date();
                this.selectedDistribusi.tgl_signed = now.toLocaleDateString('id-ID') + ' ' + String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0') + ' WIB';
                this.selectedDistribusi.qr_hash = 'BSRE-KOESNANDI-DST-' + Date.now();
                this.selectedDistribusi.status = 'Telah Diterima & Disahkan';

                // update list
                const found = this.distribusiList.find(d => d.id === this.selectedDistribusi.id);
                if (found) {
                    found.signed = true;
                    found.tgl_signed = this.selectedDistribusi.tgl_signed;
                    found.qr_hash = this.selectedDistribusi.qr_hash;
                    found.status = 'Telah Diterima & Disahkan';
                }
                alert('✅ BAST Distribusi berhasil disahkan dan ditandatangani secara digital (QR Code BSrE aktif)!');
            }
        },

        // Cetak Dokumen
        printCurrent() {
            window.print();
        }
    }" x-cloak>

        <!-- Header Banner & Mini KPI Strip -->
        <div class="no-print bg-gradient-to-r from-purple-600/15 via-slate-900 to-slate-900 border border-purple-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-6 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 text-xs font-bold mb-3">
                        <span class="w-2 h-2 rounded-full bg-purple-400 animate-pulse"></span>
                        <span>MODUL RESMI BERITA ACARA SERAH TERIMA (BAST) RSUD</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Pusat Cetak Berita Acara (BAST)</h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                        Penerbitan dokumen resmi BAST untuk penambahan aset tetap berbasis triwulan pengadaan serta BAST serah terima barang ke unit dan paviliun penerima (Sub-Admin).
                    </p>
                </div>
                
                <!-- Quick Print Buttons -->
                <div class="flex flex-wrap items-center gap-3">
                    <button type="button" @click="activeTab = 'triwulan'; openPrintTriwulan('TW2')"
                        class="px-4 py-2.5 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-purple-500/20 transition-all flex items-center space-x-2 shrink-0 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>🖨️ Cetak BAST Triwulan Aktif</span>
                    </button>
                    <button type="button" @click="activeTab = 'distribusi'; openPrintDistribusi(distribusiList[0])"
                        class="px-4 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-teal-500/20 transition-all flex items-center space-x-2 shrink-0 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        <span>🚚 Cetak BAST Distribusi Unit</span>
                    </button>
                </div>
            </div>

            <!-- Mini Summary KPI Cards Strip -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-800/80">
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-purple-500/10 text-purple-400 text-lg">🏛️</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">BAST Penambahan Triwulan</span>
                        <span class="text-sm sm:text-base font-extrabold text-white">4 Periode (1 Tahun)</span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-teal-500/10 text-teal-400 text-lg">🚚</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">BAST Distribusi Unit</span>
                        <span class="text-sm sm:text-base font-extrabold text-teal-300" x-text="distribusiList.length + ' Dokumen'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-lg">📱</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">E-Sign QR Code BSrE</span>
                        <span class="text-sm sm:text-base font-extrabold text-emerald-300">Siap Cetak Resmi</span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-amber-500/10 text-amber-400 text-lg">💰</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Aset TW II (2026)</span>
                        <span class="text-sm sm:text-base font-extrabold text-amber-300">Rp 4,66 Miliar</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- NAVIGATION DUAL TABS: TAB 1 (BAST TRIWULAN) VS TAB 2 (BAST DISTRIBUSI)    -->
        <!-- ========================================================================= -->
        <div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl p-3 shadow-xl mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                
                <!-- Tab 1 Button -->
                <button type="button" @click="activeTab = 'triwulan'"
                    class="p-4 rounded-2xl transition-all flex items-center space-x-3 text-left"
                    :class="activeTab === 'triwulan' ? 'bg-purple-500/20 text-purple-300 border-2 border-purple-500/50 shadow-lg shadow-purple-500/10' : 'bg-slate-950/60 text-slate-400 hover:text-white border border-slate-800/80'">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg font-bold shrink-0"
                         :class="activeTab === 'triwulan' ? 'bg-purple-500 text-slate-950 shadow-md shadow-purple-500/30' : 'bg-slate-900 text-slate-400 border border-slate-800'">
                        <span>🏛️</span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] font-bold uppercase tracking-wider block" :class="activeTab === 'triwulan' ? 'text-purple-400' : 'text-slate-500'">Dokumen Induk Pengadaan</span>
                        <span class="text-xs sm:text-sm font-extrabold block text-white truncate">1. BAST Penambahan ASTAP (Triwulan)</span>
                        <span class="text-[10px] text-slate-400 block truncate">Rekapitulasi 8 Kategori & Rincian Belanja Modal per Triwulan</span>
                    </div>
                </button>

                <!-- Tab 2 Button -->
                <button type="button" @click="activeTab = 'distribusi'"
                    class="p-4 rounded-2xl transition-all flex items-center space-x-3 text-left"
                    :class="activeTab === 'distribusi' ? 'bg-teal-500/20 text-teal-300 border-2 border-teal-500/50 shadow-lg shadow-teal-500/10' : 'bg-slate-950/60 text-slate-400 hover:text-white border border-slate-800/80'">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg font-bold shrink-0"
                         :class="activeTab === 'distribusi' ? 'bg-teal-500 text-slate-950 shadow-md shadow-teal-500/30' : 'bg-slate-900 text-slate-400 border border-slate-800'">
                        <span>🚚</span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] font-bold uppercase tracking-wider block" :class="activeTab === 'distribusi' ? 'text-teal-400' : 'text-slate-500'">Penyerahan ke Ruangan</span>
                        <span class="text-xs sm:text-sm font-extrabold block text-white truncate">2. BAST Distribusi Unit & Paviliun</span>
                        <span class="text-[10px] text-slate-400 block truncate">Serah Terima Barang kepada Akun Sub-Admin Paviliun / Instalasi</span>
                    </div>
                </button>

            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- KONTEN TAB 1: BAST PENAMBAHAN DATA ASTAP BERDASARKAN TRIWULAN             -->
        <!-- ========================================================================= -->
        <div x-show="activeTab === 'triwulan'" class="space-y-6" x-cloak>
            
            <!-- Filter Triwulan & Tahun Toolbar -->
            <div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    
                    <!-- Pilihan 4 Triwulan Tabs -->
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-2 shrink-0">Pilih Periode:</span>
                        
                        <button type="button" @click="selectedTriwulanKey = 'TW1'"
                            :class="selectedTriwulanKey === 'TW1' ? 'bg-purple-500 text-slate-950 font-extrabold shadow-lg shadow-purple-500/20 ring-2 ring-purple-400' : 'bg-slate-950 text-slate-300 hover:text-white border border-slate-800'"
                            class="px-4 py-2 rounded-xl text-xs font-semibold transition-all">
                            Triwulan I (Jan - Mar)
                        </button>

                        <button type="button" @click="selectedTriwulanKey = 'TW2'"
                            :class="selectedTriwulanKey === 'TW2' ? 'bg-purple-500 text-slate-950 font-extrabold shadow-lg shadow-purple-500/20 ring-2 ring-purple-400' : 'bg-slate-950 text-slate-300 hover:text-white border border-slate-800'"
                            class="px-4 py-2 rounded-xl text-xs font-semibold transition-all">
                            Triwulan II (Apr - Jun)
                        </button>

                        <button type="button" @click="selectedTriwulanKey = 'TW3'"
                            :class="selectedTriwulanKey === 'TW3' ? 'bg-purple-500 text-slate-950 font-extrabold shadow-lg shadow-purple-500/20 ring-2 ring-purple-400' : 'bg-slate-950 text-slate-300 hover:text-white border border-slate-800'"
                            class="px-4 py-2 rounded-xl text-xs font-semibold transition-all">
                            Triwulan III (Jul - Sep)
                        </button>

                        <button type="button" @click="selectedTriwulanKey = 'TW4'"
                            :class="selectedTriwulanKey === 'TW4' ? 'bg-purple-500 text-slate-950 font-extrabold shadow-lg shadow-purple-500/20 ring-2 ring-purple-400' : 'bg-slate-950 text-slate-300 hover:text-white border border-slate-800'"
                            class="px-4 py-2 rounded-xl text-xs font-semibold transition-all">
                            Triwulan IV (Okt - Des)
                        </button>
                    </div>

                    <!-- Tahun Dropdown & Tombol Cetak BAST Triwulan -->
                    <div class="flex items-center space-x-3 shrink-0">
                        <select x-model="selectedTahun" class="bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs font-bold text-white focus:outline-none focus:border-purple-500">
                            <option value="2026">Tahun Anggaran 2026</option>
                            <option value="2025">Tahun Anggaran 2025</option>
                        </select>

                        <button type="button" @click="openPrintTriwulan(selectedTriwulanKey)"
                            class="px-4 py-2 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-purple-500/20 transition-all flex items-center space-x-1.5 active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Lihat & Cetak Lembar BAST</span>
                        </button>
                    </div>

                </div>
            </div>

            <!-- Card Ringkasan Triwulan Aktif -->
            <div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-4">
                    <div>
                        <div class="inline-flex items-center space-x-2 px-2.5 py-0.5 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 text-[10px] font-bold mb-1">
                            <span x-text="'DOKUMEN BAST RESMI: ' + currentTriwulanDoc.nomor_surat"></span>
                        </div>
                        <h2 class="text-lg font-extrabold text-white" x-text="'Berita Acara Penambahan Aset Tetap - ' + currentTriwulanDoc.triwulan_nama"></h2>
                        <p class="text-xs text-slate-400 mt-0.5" x-text="'Hari & Tanggal Pelaksanaan: ' + currentTriwulanDoc.hari_tanggal"></p>
                    </div>

                    <div class="flex items-center space-x-3">
                        <div class="text-right">
                            <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Realisasi Triwulan Ini</span>
                            <span class="text-base sm:text-lg font-black text-emerald-400 font-mono" x-text="'Rp ' + formatRupiah(currentTriwulanTotalNilai)"></span>
                        </div>
                        <span class="px-3 py-1.5 rounded-xl text-xs font-bold border"
                              :class="currentTriwulanDoc.pihak2_signed ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border-amber-500/30'">
                            <span x-text="currentTriwulanDoc.pihak2_signed ? '✅ Disahkan' : '⏳ Draf / Menunggu TTD'"></span>
                        </span>
                    </div>
                </div>

                <!-- Bagian 1: TABEL REKAPITULASI 8 KELOMPOK ASET (FORMAT ASLI STANDAR BAST) -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-slate-200 uppercase tracking-wider flex items-center space-x-2">
                            <span>📊 1. REKAPITULASI 8 KELOMPOK ASET TETAP TRIWULAN INI:</span>
                        </span>
                        <span class="text-[11px] text-purple-400 font-mono font-bold" x-text="'Total: ' + formatNumber(currentTriwulanTotalQty) + ' Barang / Aset'"></span>
                    </div>
                    
                    <div class="overflow-x-auto rounded-2xl border border-slate-700 shadow-xl">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="bg-slate-950 text-slate-300 font-bold uppercase tracking-wider border-b border-slate-700">
                                <tr>
                                    <th class="px-3 py-2.5 text-center w-12 border-r border-slate-800">No</th>
                                    <th class="px-4 py-2.5 border-r border-slate-800">Nama Kelompok / Kategori Aset</th>
                                    <th class="px-4 py-2.5 text-center border-r border-slate-800 w-32">Kuantitas</th>
                                    <th class="px-4 py-2.5 text-right w-48">Nilai Realisasi Perolehan (Rp)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800 bg-slate-900/60 font-medium">
                                <template x-for="item in currentTriwulanDoc.rekapItems" :key="item.no">
                                    <tr class="hover:bg-slate-800/40 transition-colors">
                                        <td class="px-3 py-2.5 text-center text-slate-400 font-mono border-r border-slate-800" x-text="item.no"></td>
                                        <td class="px-4 py-2.5 font-semibold text-slate-200 border-r border-slate-800" x-text="item.nama"></td>
                                        <td class="px-4 py-2.5 text-center font-mono font-bold text-cyan-400 border-r border-slate-800" x-text="formatNumber(item.qty) + ' Barang'"></td>
                                        <td class="px-4 py-2.5 text-right font-mono font-bold" :class="item.nilai > 0 ? 'text-amber-300' : 'text-slate-500'" x-text="'Rp ' + formatRupiah(item.nilai)"></td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot class="bg-slate-950 text-white font-extrabold border-t-2 border-slate-700">
                                <tr>
                                    <td colspan="2" class="px-4 py-3 text-center uppercase tracking-wider text-purple-300 border-r border-slate-800">Total Pengadaan Triwulan Terpilih:</td>
                                    <td class="px-4 py-3 text-center font-mono text-cyan-300 text-sm border-r border-slate-800" x-text="formatNumber(currentTriwulanTotalQty) + ' Barang'"></td>
                                    <td class="px-4 py-3 text-right font-mono text-emerald-400 text-sm" x-text="'Rp ' + formatRupiah(currentTriwulanTotalNilai)"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Bagian 2: TABEL RINCIAN DETAIL BARANG YANG DIADAKAN PADA TRIWULAN INI -->
                <div class="pt-4 border-t border-slate-800 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider flex items-center space-x-2">
                                <span>📦 2. DAFTAR RINCIAN BARANG & BELANJA MODAL YANG DIADAKAN PADA TRIWULAN INI:</span>
                            </span>
                            <p class="text-[11px] text-slate-400 mt-0.5">Daftar item belanja modal yang dibukukan lengkap dengan dokumen SPK dan nilai realisasinya.</p>
                        </div>

                        <!-- Search Box Filter Barang -->
                        <div class="relative w-full sm:w-64">
                            <input type="text" x-model="searchBarangTriwulan" placeholder="Cari nama barang / kode 108 / penyedia..."
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-1.5 pl-8 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-purple-500">
                            <svg class="w-3.5 h-3.5 text-purple-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded-2xl border border-slate-700 shadow-xl">
                        <table class="w-full text-left text-xs border-collapse min-w-[900px]">
                            <thead class="bg-slate-950 text-slate-300 font-bold uppercase tracking-wider border-b border-slate-700 text-[11px]">
                                <tr>
                                    <th class="px-3 py-2.5 text-center w-10 border-r border-slate-800">No</th>
                                    <th class="px-3 py-2.5 border-r border-slate-800">Tgl & No SPK</th>
                                    <th class="px-3 py-2.5 border-r border-slate-800">Kode Barang 108</th>
                                    <th class="px-4 py-2.5 border-r border-slate-800">Nama Barang & Spesifikasi</th>
                                    <th class="px-3 py-2.5 border-r border-slate-800">Penyedia / Rekanan</th>
                                    <th class="px-3 py-2.5 text-center border-r border-slate-800">Volume</th>
                                    <th class="px-4 py-2.5 text-right">Nilai Realisasi (Rp)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800 bg-slate-900/60 font-medium text-[11px]">
                                <template x-for="(b, idx) in filteredDetailBarangTriwulan" :key="b.no">
                                    <tr class="hover:bg-slate-800/40 transition-colors">
                                        <td class="px-3 py-2.5 text-center text-slate-400 font-mono border-r border-slate-800" x-text="idx + 1"></td>
                                        <td class="px-3 py-2.5 border-r border-slate-800">
                                            <div class="font-mono font-bold text-purple-300" x-text="b.nomor_spk"></div>
                                            <div class="text-[10px] text-slate-400" x-text="b.tanggal_sp2d"></div>
                                        </td>
                                        <td class="px-3 py-2.5 font-mono text-cyan-400 font-bold border-r border-slate-800" x-text="b.kode_108"></td>
                                        <td class="px-4 py-2.5 border-r border-slate-800">
                                            <div class="font-bold text-white" x-text="b.nama_barang"></div>
                                            <div class="text-[10px] text-slate-400" x-text="b.spesifikasi"></div>
                                        </td>
                                        <td class="px-3 py-2.5 text-slate-300 border-r border-slate-800 font-semibold" x-text="b.penyedia"></td>
                                        <td class="px-3 py-2.5 text-center font-mono font-bold text-white border-r border-slate-800" x-text="b.volume + ' ' + b.satuan"></td>
                                        <td class="px-4 py-2.5 text-right font-mono font-bold text-emerald-400" x-text="'Rp ' + formatRupiah(b.nilai_realisasi)"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- KONTEN TAB 2: BAST DISTRIBUSI BARANG KE UNIT & PAVILIUN (SUB ADMIN)       -->
        <!-- ========================================================================= -->
        <div x-show="activeTab === 'distribusi'" class="space-y-6" x-cloak>
            
            <!-- Toolbar & Filter Distribusi -->
            <div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    
                    <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                        <div class="relative flex-1 sm:w-80">
                            <input type="text" x-model="distribusiSearch" placeholder="Cari nomor BAST / unit paviliun / nama PJ sub-admin..."
                                class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-2.5 pl-10 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-teal-500">
                            <svg class="w-4 h-4 text-teal-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <select x-model="distribusiUnitFilter" class="bg-slate-950 border border-slate-800 rounded-2xl px-3 py-2.5 text-xs text-slate-300 focus:outline-none focus:border-teal-500">
                            <option value="all">Semua Unit / Paviliun (Sub-Admin)</option>
                            <option value="Paviliun Graha Amukti">Paviliun Graha Amukti</option>
                            <option value="Instalasi Gawat Darurat (IGD)">Instalasi Gawat Darurat (IGD)</option>
                            <option value="Instalasi Radiologi & Imaging">Instalasi Radiologi & Imaging</option>
                            <option value="Instalasi Rawat Intensif (ICU)">Instalasi Rawat Intensif (ICU)</option>
                        </select>
                    </div>

                    <a href="{{ route('distribusi.create') }}"
                        class="px-4 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold text-xs shadow-lg shadow-teal-500/20 transition-all flex items-center space-x-2 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>+ Input Distribusi Baru</span>
                    </a>

                </div>
            </div>

            <!-- Tabel Daftar BAST Distribusi ke Unit / Ruangan -->
            <div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6 overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="px-4 py-3.5 text-center w-12">No</th>
                            <th class="px-4 py-3.5 text-left">Nomor BAST Distribusi</th>
                            <th class="px-4 py-3.5 text-left">Unit / Paviliun (Penerima)</th>
                            <th class="px-4 py-3.5 text-left">Kepala Ruangan / PJ (Sub Admin)</th>
                            <th class="px-4 py-3.5 text-center">Jumlah Barang</th>
                            <th class="px-4 py-3.5 text-center">Status BAST</th>
                            <th class="px-4 py-3.5 text-center">Aksi Cetak</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        <template x-for="(item, index) in filteredDistribusiList" :key="item.id">
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-4 text-center font-bold text-slate-400" x-text="index + 1"></td>
                                <td class="px-4 py-4">
                                    <div class="font-mono font-bold text-teal-400" x-text="item.nomor_bast"></div>
                                    <div class="text-[10px] text-slate-400" x-text="item.tgl_bast"></div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="font-bold text-white" x-text="item.unit_nama"></div>
                                    <div class="text-[10px] text-slate-400" x-text="item.unit_tipe"></div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="font-semibold text-emerald-400" x-text="item.pj_nama"></div>
                                    <div class="text-[10px] text-slate-400 font-mono" x-text="'NIP. ' + item.pj_nip"></div>
                                </td>
                                <td class="px-4 py-4 text-center font-mono font-bold text-white" x-text="item.items.reduce((s, i) => s + i.qty, 0) + ' Unit'"></td>
                                <td class="px-4 py-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border inline-flex items-center space-x-1"
                                          :class="item.signed ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border-amber-500/30'">
                                        <span x-text="item.signed ? '✅ Disahkan (QR Code)' : '⏳ Belum TTD'"></span>
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center space-x-1.5 whitespace-nowrap">
                                    <button type="button" @click="openPrintDistribusi(item)"
                                        class="px-3 py-1.5 rounded-xl bg-teal-500/20 hover:bg-teal-500/30 text-teal-300 border border-teal-500/40 font-bold text-xs transition-all inline-flex items-center space-x-1.5 shadow-sm active:scale-95">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        <span>🖨️ Cetak Lembar BAST</span>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- MODAL CETAK 1: LEMBAR DOKUMEN BAST PENAMBAHAN ASTAP (TRIWULAN PENGADAAN)  -->
        <!-- ========================================================================= -->
        <div x-show="showPrintTriwulanModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-2 sm:p-4 overflow-y-auto" x-cloak>
            <div @click.away="showPrintTriwulanModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-4xl w-full p-4 sm:p-6 shadow-2xl space-y-4 my-auto relative">
                
                <!-- Action Bar Modal -->
                <div class="no-print flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
                    <div class="flex items-center space-x-2">
                        <span class="p-2 rounded-xl bg-purple-500/20 text-purple-300 text-sm">🖨️</span>
                        <div>
                            <h3 class="text-base font-extrabold text-white">Cetak Berita Acara Penambahan ASTAP (Triwulan)</h3>
                            <p class="text-[11px] text-slate-400">Dokumen Resmi Pengesahan Hasil Belanja Modal RSUD Dr. H. Koesnandi</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 w-full sm:w-auto justify-end">
                        <button type="button" @click="showEditTriwulanForm = !showEditTriwulanForm"
                            class="px-3.5 py-2 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 font-bold text-xs transition-all flex items-center space-x-1.5 active:scale-95">
                            <span x-text="showEditTriwulanForm ? '✕ Tutup Form Edit' : '✏️ Edit Nama Pejabat / Data Surat'"></span>
                        </button>
                        <button type="button" @click="printCurrent()"
                            class="px-4 py-2 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-purple-500/20 transition-all flex items-center space-x-1.5 active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Cetak (Print / PDF)</span>
                        </button>
                        <button type="button" @click="showPrintTriwulanModal = false" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all active:scale-95">
                            Tutup
                        </button>
                    </div>
                </div>

                <!-- Formulir Cepat Edit Pejabat & Data Surat (Hidden when Printed) -->
                <div x-show="showEditTriwulanForm" class="no-print bg-slate-950 p-4 rounded-2xl border border-purple-500/40 text-xs space-y-3 shadow-inner">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                        <span class="font-bold text-purple-300 text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                            <span>✏️ Sesuaikan Data Nomor Surat, Tanggal & Identitas Pejabat Pengesah:</span>
                        </span>
                        <span class="text-[10px] text-emerald-400 font-mono">Teks di lembar BAST otomatis berganti live</span>
                    </div>

                    <!-- Edit Nomor & Tanggal -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Nomor Surat BAST</label>
                            <input type="text" x-model="currentTriwulanDoc.nomor_surat" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-purple-300 font-mono font-bold text-xs">
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Hari & Tanggal Pelaksanaan</label>
                            <input type="text" x-model="currentTriwulanDoc.hari_tanggal" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white text-xs">
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Nama Periode Triwulan</label>
                            <input type="text" x-model="currentTriwulanDoc.triwulan_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white text-xs">
                        </div>
                    </div>

                    <!-- Edit 3 Pejabat: PPK, Pengurus Barang, Direktur -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-2 border-t border-slate-800/80">
                        <!-- Pihak 1 (PPK) -->
                        <div class="p-3 rounded-xl bg-slate-900/80 border border-purple-500/30 space-y-2">
                            <span class="text-[10px] font-bold text-purple-400 block uppercase">1. Pihak Kesatu (PPK):</span>
                            <div>
                                <label class="block text-slate-400 text-[9px]">Nama Lengkap & Gelar</label>
                                <input type="text" x-model="currentTriwulanDoc.pihak1_nama" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white font-semibold text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[9px]">NIP PPK</label>
                                <input type="text" x-model="currentTriwulanDoc.pihak1_nip" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white font-mono text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[9px]">Jabatan PPK</label>
                                <input type="text" x-model="currentTriwulanDoc.pihak1_jabatan" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white text-xs">
                            </div>
                        </div>

                        <!-- Pihak 2 (Pengurus Barang) -->
                        <div class="p-3 rounded-xl bg-slate-900/80 border border-emerald-500/30 space-y-2">
                            <span class="text-[10px] font-bold text-emerald-400 block uppercase">2. Pihak Kedua (Pengurus Barang):</span>
                            <div>
                                <label class="block text-slate-400 text-[9px]">Nama Pengurus Barang</label>
                                <input type="text" x-model="currentTriwulanDoc.pihak2_nama" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-emerald-400 font-bold text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[9px]">NIP Pengurus Barang</label>
                                <input type="text" x-model="currentTriwulanDoc.pihak2_nip" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white font-mono text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[9px]">Jabatan Pengurus</label>
                                <input type="text" x-model="currentTriwulanDoc.pihak2_jabatan" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white text-xs">
                            </div>
                        </div>

                        <!-- Direktur RSUD -->
                        <div class="p-3 rounded-xl bg-slate-900/80 border border-cyan-500/30 space-y-2">
                            <span class="text-[10px] font-bold text-cyan-400 block uppercase">3. Mengetahui (Direktur RSUD):</span>
                            <div>
                                <label class="block text-slate-400 text-[9px]">Nama Direktur & Gelar</label>
                                <input type="text" x-model="currentTriwulanDoc.direktur_nama" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white font-bold text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[9px]">NIP Direktur</label>
                                <input type="text" x-model="currentTriwulanDoc.direktur_nip" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white font-mono text-xs">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- LEMBAR DOKUMEN CETAK ASLI RESMI (KERTAS PUTIH STANDAR RUMAH SAKIT) -->
                <div id="print-area-triwulan" class="bg-white text-black p-6 sm:p-10 rounded-2xl shadow-xl max-h-[70vh] overflow-y-auto font-serif text-[11px] leading-relaxed select-text print:max-h-none print:overflow-visible print:p-0 print:m-0 print:shadow-none print:rounded-none">
                    
                    <!-- KOP SURAT RESMI -->
                    <div class="border-b-[3px] border-black pb-1 mb-0.5">
                        <div class="flex items-center justify-between gap-4">
                            <!-- Logo RSUD -->
                            <div class="w-20 shrink-0 flex justify-center">
                                <img src="{{ asset('img/Logo-rsud/logo-rsud.png') }}" alt="Logo RSUD" class="w-16 h-16 object-contain">
                            </div>

                            <!-- Teks Header Kop -->
                            <div class="flex-1 text-center font-sans text-black">
                                <h4 class="font-bold text-xs sm:text-sm uppercase tracking-wide leading-tight">PEMERINTAH KABUPATEN BONDOWOSO</h4>
                                <h3 class="font-black text-sm sm:text-base uppercase tracking-tight leading-tight">RUMAH SAKIT UMUM DAERAH dr.H.KOESNADI</h3>
                                <p class="text-[10px] leading-tight mt-0.5">Jl. Kapten Piere Tendean No. 3 Telp. (0332) 421974 Fax.0332 422311</p>
                                <p class="text-[10px] leading-tight">e-mail : rsu.koesnadi@gmail.com, Website : rsudrkoesnadi.go.id</p>
                                <h4 class="font-bold text-xs tracking-[0.3em] uppercase mt-0.5">B O N D O W O S O</h4>
                            </div>

                            <div class="w-16 shrink-0"></div>
                        </div>
                    </div>
                    <div class="border-b border-black mb-4"></div>

                    <!-- JUDUL & NOMOR SURAT -->
                    <div class="text-center font-sans mb-3">
                        <h3 class="font-black text-xs sm:text-sm uppercase underline tracking-wider">BERITA ACARA PENERIMAAN DAN PEMBUKUAN HASIL PENGADAAN BARANG (ASTAP)</h3>
                        <p class="text-[11px] font-semibold">Nomor : <span x-text="currentTriwulanDoc.nomor_surat"></span></p>
                    </div>

                    <!-- PARAGRAF PEMBUKA -->
                    <p class="text-justify mb-2 indent-6">
                        Pada hari ini <span class="font-semibold" x-text="currentTriwulanDoc.hari_tanggal"></span> bertempat di <span class="font-semibold" x-text="currentTriwulanDoc.lokasi"></span>, yang bertanda tangan dibawah ini :
                    </p>

                    <!-- IDENTITAS PIHAK KESATU & KEDUA -->
                    <div class="space-y-1.5 mb-3 ml-2">
                        <div class="flex items-start">
                            <span class="w-5 font-bold">1.</span>
                            <div class="w-28 font-semibold">Nama</div>
                            <div class="w-3">:</div>
                            <div class="flex-1 font-bold" x-text="currentTriwulanDoc.pihak1_nama"></div>
                        </div>
                        <div class="flex items-start">
                            <span class="w-5"></span>
                            <div class="w-28 font-semibold">NIP</div>
                            <div class="w-3">:</div>
                            <div class="flex-1 font-mono" x-text="currentTriwulanDoc.pihak1_nip"></div>
                        </div>
                        <div class="flex items-start">
                            <span class="w-5"></span>
                            <div class="w-28 font-semibold">Jabatan</div>
                            <div class="w-3">:</div>
                            <div class="flex-1" x-text="currentTriwulanDoc.pihak1_jabatan"></div>
                        </div>
                        <div class="flex items-start">
                            <span class="w-5"></span>
                            <div class="w-28"></div>
                            <div class="w-3"></div>
                            <div class="flex-1 italic font-semibold">Selanjutnya disebut sebagai PIHAK KESATU</div>
                        </div>

                        <div class="flex items-start pt-1.5">
                            <span class="w-5 font-bold">2.</span>
                            <div class="w-28 font-semibold">Nama</div>
                            <div class="w-3">:</div>
                            <div class="flex-1 font-bold" x-text="currentTriwulanDoc.pihak2_nama"></div>
                        </div>
                        <div class="flex items-start">
                            <span class="w-5"></span>
                            <div class="w-28 font-semibold">NIP</div>
                            <div class="w-3">:</div>
                            <div class="flex-1 font-mono" x-text="currentTriwulanDoc.pihak2_nip"></div>
                        </div>
                        <div class="flex items-start">
                            <span class="w-5"></span>
                            <div class="w-28 font-semibold">Jabatan</div>
                            <div class="w-3">:</div>
                            <div class="flex-1" x-text="currentTriwulanDoc.pihak2_jabatan"></div>
                        </div>
                        <div class="flex items-start">
                            <span class="w-5"></span>
                            <div class="w-28"></div>
                            <div class="w-3"></div>
                            <div class="flex-1 italic font-semibold">Selanjutnya disebut sebagai PIHAK KEDUA</div>
                        </div>
                    </div>

                    <!-- KLAUSUL PASAL -->
                    <p class="text-justify mb-2 indent-6">
                        PIHAK KESATU telah menyerahkan kepada PIHAK KEDUA, dan PIHAK KEDUA menyatakan telah menerima penyerahan barang hasil pengadaan Belanja Modal pada <span class="font-bold" x-text="currentTriwulanDoc.triwulan_nama"></span> dengan rekapitulasi serta rincian barang sebagai berikut :
                    </p>

                    <!-- TABEL 1: REKAPITULASI 8 KELOMPOK BARANG ASET -->
                    <div class="my-3">
                        <table class="w-full text-center border-collapse border border-black text-[10px] font-sans">
                            <thead>
                                <tr class="bg-gray-200 font-bold border-b border-black">
                                    <th class="border border-black px-2 py-1 w-10">No</th>
                                    <th class="border border-black px-4 py-1 text-left">Nama Kelompok / Kategori Aset</th>
                                    <th class="border border-black px-3 py-1 w-28">Jumlah (Volume)</th>
                                    <th class="border border-black px-4 py-1 text-right w-44">Nilai Perolehan (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="r in currentTriwulanDoc.rekapItems" :key="r.no">
                                    <tr>
                                        <td class="border border-black px-2 py-1 font-mono" x-text="r.no"></td>
                                        <td class="border border-black px-4 py-1 text-left font-medium" x-text="r.nama"></td>
                                        <td class="border border-black px-3 py-1 font-mono" x-text="r.qty > 0 ? (formatNumber(r.qty) + ' Barang') : '-'"></td>
                                        <td class="border border-black px-4 py-1 text-right font-mono" x-text="r.nilai > 0 ? formatRupiah(r.nilai) : '-'"></td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-100 font-bold border-t-2 border-black">
                                    <th colspan="2" class="border border-black px-4 py-1 text-center uppercase">Total Belanja Modal:</th>
                                    <th class="border border-black px-3 py-1 font-mono" x-text="formatNumber(currentTriwulanTotalQty) + ' Barang'"></th>
                                    <th class="border border-black px-4 py-1 text-right font-mono" x-text="'Rp ' + formatRupiah(currentTriwulanTotalNilai)"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- TABEL 2: DETAIL RINCIAN BARANG YANG DIADAKAN PADA TRIWULAN INI -->
                    <div class="my-3">
                        <p class="font-sans font-bold text-[10px] mb-1">Rincian Barang yang Diadakan dalam <span x-text="currentTriwulanDoc.triwulan_nama"></span>:</p>
                        <table class="w-full text-center border-collapse border border-black text-[9px] font-sans">
                            <thead>
                                <tr class="bg-gray-200 font-bold border-b border-black">
                                    <th class="border border-black px-1.5 py-1 w-8">No</th>
                                    <th class="border border-black px-2 py-1 text-left">Kode Barang 108</th>
                                    <th class="border border-black px-3 py-1 text-left">Nama Barang & Spesifikasi</th>
                                    <th class="border border-black px-2 py-1 text-left">Penyedia / Rekanan</th>
                                    <th class="border border-black px-1.5 py-1 w-14">Vol</th>
                                    <th class="border border-black px-2.5 py-1 text-right w-28">Nilai Realisasi (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(b, idx) in currentTriwulanDoc.detailBarang" :key="b.no">
                                    <tr>
                                        <td class="border border-black px-1.5 py-1 font-mono" x-text="idx + 1"></td>
                                        <td class="border border-black px-2 py-1 text-left font-mono" x-text="b.kode_108"></td>
                                        <td class="border border-black px-3 py-1 text-left font-medium" x-text="b.nama_barang + ' (' + b.spesifikasi + ')'"></td>
                                        <td class="border border-black px-2 py-1 text-left" x-text="b.penyedia"></td>
                                        <td class="border border-black px-1.5 py-1 font-mono" x-text="b.volume + ' ' + b.satuan"></td>
                                        <td class="border border-black px-2.5 py-1 text-right font-mono font-semibold" x-text="formatRupiah(b.nilai_realisasi)"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <!-- PENUTUP SURAT -->
                    <p class="text-justify mb-4 indent-6">
                        Demikian Berita Acara Penerimaan dan Pembukuan Hasil Pengadaan ini dibuat rangkap 2 (dua) untuk dipergunakan sebagaimana mestinya.
                    </p>

                    <!-- AREA TANDA TANGAN (KIRI: PIHAK KEDUA, KANAN: PIHAK KESATU, BAWAH TENGAH: DIREKTUR) -->
                    <div class="grid grid-cols-2 gap-4 text-center font-sans text-[10px] mt-4">
                        <!-- Pihak Kedua -->
                        <div class="space-y-1">
                            <p class="font-bold">Yang Menerima,</p>
                            <p class="font-semibold uppercase text-[9.5px]">PENGURUS BARANG ASET</p>
                            
                            <!-- E-Sign QR Code -->
                            <div class="h-20 flex items-center justify-center py-1">
                                <template x-if="currentTriwulanDoc.pihak2_signed">
                                    <div class="flex items-center space-x-2 p-1.5 border border-emerald-600 bg-emerald-50 rounded">
                                        <div class="w-12 h-12 bg-white border border-black p-0.5 flex items-center justify-center">
                                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=BSRE-RSUD-KOESNANDI-BAST-TW" class="w-full h-full object-contain">
                                        </div>
                                        <div class="text-left text-[7.5px] leading-tight text-emerald-950 font-sans">
                                            <div class="font-bold">DITANDATANGANI SECARA ELEKTRONIK</div>
                                            <div>Balai Sertifikasi Elektronik (BSrE)</div>
                                            <div class="font-mono" x-text="currentTriwulanDoc.pihak2_tgl_ttd"></div>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!currentTriwulanDoc.pihak2_signed">
                                    <div class="h-16 flex items-center justify-center text-slate-400 italic text-[10px]">
                                        (Tanda Tangan & Cap Basah)
                                    </div>
                                </template>
                            </div>

                            <p class="font-bold underline text-[10.5px]" x-text="currentTriwulanDoc.pihak2_nama"></p>
                            <p class="font-mono text-[9px]" x-text="'NIP. ' + currentTriwulanDoc.pihak2_nip"></p>
                        </div>

                        <!-- Pihak Kesatu -->
                        <div class="space-y-1">
                            <p class="font-bold">Yang Menyerahkan,</p>
                            <p class="font-semibold uppercase text-[9.5px]">PEJABAT PEMBUAT KOMITMEN (PPK)</p>
                            
                            <!-- E-Sign QR Code PPK -->
                            <div class="h-20 flex items-center justify-center py-1">
                                <div class="flex items-center space-x-2 p-1.5 border border-purple-600 bg-purple-50 rounded">
                                    <div class="w-12 h-12 bg-white border border-black p-0.5 flex items-center justify-center">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=BSRE-RSUD-KOESNANDI-PPK-TW" class="w-full h-full object-contain">
                                    </div>
                                    <div class="text-left text-[7.5px] leading-tight text-purple-950 font-sans">
                                        <div class="font-bold">DITANDATANGANI SECARA ELEKTRONIK</div>
                                        <div>Balai Sertifikasi Elektronik (BSrE)</div>
                                        <div class="font-mono">Terverifikasi SIPD & SIMAT</div>
                                    </div>
                                </div>
                            </div>

                            <p class="font-bold underline text-[10.5px]" x-text="currentTriwulanDoc.pihak1_nama"></p>
                            <p class="font-mono text-[9px]" x-text="'NIP. ' + currentTriwulanDoc.pihak1_nip"></p>
                        </div>
                    </div>

                    <!-- Mengetahui Direktur RSUD -->
                    <div class="text-center font-sans text-[10px] mt-4 pt-2 border-t border-dotted border-gray-400">
                        <p class="font-bold">Mengetahui / Mengesahkan,</p>
                        <p class="font-black uppercase text-[10px]">DIREKTUR RSUD dr.H.KOESNADI BONDOWOSO</p>
                        
                        <div class="h-16 flex items-center justify-center py-1">
                            <div class="flex items-center space-x-2 p-1 border border-black bg-gray-50 rounded">
                                <div class="w-10 h-10 bg-white border border-black p-0.5">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=BSRE-DIREKTUR-RSUD-KOESNANDI" class="w-full h-full object-contain">
                                </div>
                                <div class="text-left text-[7px] leading-tight text-black font-sans">
                                    <div class="font-bold">PENGGUNA BARANG / DIREKTUR</div>
                                    <div>Tervalidasi Digital Signature</div>
                                </div>
</p>

                        <p class="font-bold underline text-[11px]" x-text="currentTriwulanDoc.direktur_nama"></p>
                        <p class="font-mono text-[9px]" x-text="'NIP. ' + currentTriwulanDoc.direktur_nip"></p>
                    </div>

                </div>

            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL CETAK 2: LEMBAR DOKUMEN BAST PENYERAHAN BARANG (DISTRIBUSI RUANGAN) -->
        <!-- ========================================================================= -->
        <div x-show="showPrintDistribusiModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-2 sm:p-4 overflow-y-auto" x-cloak>
            <div @click.away="showPrintDistribusiModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-4xl w-full p-4 sm:p-6 shadow-2xl space-y-4 my-auto relative">
                
                <!-- Action Bar Modal -->
                <div class="no-print flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
                    <div class="flex items-center space-x-2">
                        <span class="p-2 rounded-xl bg-teal-500/20 text-teal-300 text-sm">🚚</span>
                        <div>
                            <h3 class="text-base font-extrabold text-white">Berita Acara Penyerahan Barang (Distribusi)</h3>
                            <p class="text-[11px] text-slate-400" x-text="selectedDistribusi ? ('Penyerahan kepada: ' + selectedDistribusi.pj_nama + ' (' + selectedDistribusi.unit_nama + ')') : ''"></p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 w-full sm:w-auto justify-end">
                        <template x-if="selectedDistribusi && !selectedDistribusi.signed">
                            <button type="button" @click="signDistribusi()"
                                class="px-3 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-1.5 active:scale-95">
                                <span>✍️ Sahkan & Terbitkan QR Code</span>
                            </button>
                        </template>

                        <button type="button" @click="showEditDistribusiForm = !showEditDistribusiForm"
                            class="px-3.5 py-2 rounded-xl bg-teal-500/20 hover:bg-teal-500/30 text-teal-300 border border-teal-500/40 font-bold text-xs transition-all flex items-center space-x-1.5 active:scale-95">
                            <span x-text="showEditDistribusiForm ? '✕ Tutup Form Edit' : '✏️ Edit Data & Pejabat BAST'"></span>
                        </button>

                        <button type="button" @click="printCurrent()"
                            class="px-4 py-2 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-teal-500/20 transition-all flex items-center space-x-1.5 active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Cetak (Print / PDF)</span>
                        </button>
                        <button type="button" @click="showPrintDistribusiModal = false" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all active:scale-95">
                            Tutup
                        </button>
                    </div>
                </div>

                <!-- Formulir Cepat Edit Pejabat & Data Surat Distribusi (Hidden when Printed) -->
                <template x-if="selectedDistribusi">
                    <div x-show="showEditDistribusiForm" class="no-print bg-slate-950 p-4 rounded-2xl border border-teal-500/40 text-xs space-y-3 shadow-inner">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                            <span class="font-bold text-teal-300 text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                                <span>✏️ Sesuaikan Data Nomor Surat, Tanggal & Identitas Pengurus / Penerima Ruangan:</span>
                            </span>
                            <span class="text-[10px] text-emerald-400 font-mono">Teks di lembar BAST otomatis berganti live</span>
                        </div>

                        <!-- Edit Nomor Surat & Waktu Pelaksanaan -->
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-2.5">
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Nomor BAST Distribusi</label>
                                <input type="text" x-model="selectedDistribusi.nomor_bast" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-teal-300 font-mono font-bold text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Hari</label>
                                <input type="text" x-model="selectedDistribusi.hari" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Tanggal & Bulan</label>
                                <div class="flex space-x-1">
                                    <input type="text" x-model="selectedDistribusi.tanggal_angka" placeholder="13" class="w-12 bg-slate-900 border border-slate-700 rounded-lg px-2 py-1.5 text-white text-xs text-center font-bold">
                                    <input type="text" x-model="selectedDistribusi.bulan" placeholder="Agustus" class="flex-1 bg-slate-900 border border-slate-700 rounded-lg px-2 py-1.5 text-white text-xs">
                                </div>
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Tahun</label>
                                <input type="text" x-model="selectedDistribusi.tahun" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white text-xs text-center font-mono font-bold">
                            </div>
                        </div>

                        <!-- Edit Dasar Hukum SK Bupati -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 pt-1">
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Tahun Anggaran Aset</label>
                                <input type="text" x-model="selectedDistribusi.tahun_anggaran" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white text-xs font-mono">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Nomor SK Bupati Bondowoso</label>
                                <input type="text" x-model="selectedDistribusi.sk_bupati_nomor" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white text-xs font-mono">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Tanggal SK Bupati</label>
                                <input type="text" x-model="selectedDistribusi.sk_bupati_tanggal" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white text-xs">
                            </div>
                        </div>

                        <!-- 2 Pejabat: Pengurus Barang & Yang Menerima (Sub Admin) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-2 border-t border-slate-800/80">
                            <!-- Pihak 1 (Pengurus Barang) -->
                            <div class="p-3 rounded-xl bg-slate-900/80 border border-emerald-500/30 space-y-2">
                                <span class="text-[10px] font-bold text-emerald-400 block uppercase">1. Yang Menyerahkan (Pengurus Barang):</span>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[9px]">Nama Lengkap & Gelar</label>
                                        <input type="text" x-model="selectedDistribusi.pengurus_nama" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-emerald-400 font-bold text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px]">NIP</label>
                                        <input type="text" x-model="selectedDistribusi.pengurus_nip" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white font-mono text-xs">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[9px]">Jabatan</label>
                                        <input type="text" x-model="selectedDistribusi.pengurus_jabatan" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px]">Ruangan / Unit Kerja</label>
                                        <input type="text" x-model="selectedDistribusi.pengurus_ruangan" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white text-xs">
                                    </div>
                                </div>
                            </div>

                            <!-- Pihak 2 (Kepala Ruangan Sub-Admin) -->
                            <div class="p-3 rounded-xl bg-slate-900/80 border border-teal-500/30 space-y-2">
                                <span class="text-[10px] font-bold text-teal-400 block uppercase">2. Yang Menerima (Sub-Admin / Ruangan):</span>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[9px]">Nama PJ Ruangan / Penerima</label>
                                        <input type="text" x-model="selectedDistribusi.pj_nama" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-teal-300 font-bold text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px]">NIP</label>
                                        <input type="text" x-model="selectedDistribusi.pj_nip" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white font-mono text-xs">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[9px]">Jabatan</label>
                                        <input type="text" x-model="selectedDistribusi.pj_jabatan" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px]">Ruangan / Unit</label>
                                        <input type="text" x-model="selectedDistribusi.pj_ruangan" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white text-xs font-bold">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[9px]">Judul Tanda Tangan Penerima</label>
                                    <input type="text" x-model="selectedDistribusi.pj_jabatan_ttd" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white text-xs">
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- LEMBAR DOKUMEN CETAK ASLI BAST DISTRIBUSI (KERTAS PUTIH STANDAR RUMAH SAKIT) -->
                <template x-if="selectedDistribusi">
                    <div id="print-area-distribusi" class="bg-white text-black p-6 sm:p-10 rounded-2xl shadow-xl max-h-[70vh] overflow-y-auto font-serif text-[11px] leading-relaxed select-text print:max-h-none print:overflow-visible print:p-0 print:m-0 print:shadow-none print:rounded-none">
                        
                        <!-- KOP SURAT RESMI RSUD -->
                        <div class="border-b-[3px] border-black pb-1 mb-0.5">
                            <div class="flex items-center justify-between gap-4">
                                <!-- Logo Pemkab Bondowoso -->
                                <div class="w-20 shrink-0 flex justify-center">
                                    <img src="{{ asset('img/Logo-rsud/logo-rsud.png') }}" alt="Logo Daerah" class="w-16 h-16 object-contain">
                                </div>

                                <!-- Teks Header Kop -->
                                <div class="flex-1 text-center font-sans text-black">
                                    <h4 class="font-bold text-xs sm:text-sm uppercase tracking-wide leading-tight">PEMERINTAH KABUPATEN BONDOWOSO</h4>
                                    <h3 class="font-black text-sm sm:text-base uppercase tracking-tight leading-tight">RUMAH SAKIT UMUM DAERAH dr. H. KOESNADI</h3>
                                    <p class="text-[10px] leading-tight mt-0.5">Jl. Kapten Pierre Tendean No. 3 Telepon (0332) 421974. Fax.0332 422311</p>
                                    <p class="text-[10px] leading-tight">Website: rsudrkoesnadi.go.id, Email: rsu.koesnadi@gmail.com</p>
                                    <div class="flex items-center justify-between mt-0.5 px-4">
                                        <span class="text-[9px] font-sans"></span>
                                        <h4 class="font-bold text-xs tracking-[0.3em] uppercase">B O N D O W O S O</h4>
                                        <span class="text-[9.5px] font-sans font-semibold">Kode Pos: 68214</span>
                                    </div>
                                </div>

                                <!-- Logo RSUD Koesnandi -->
                                <div class="w-20 shrink-0 flex justify-center">
                                    <img src="{{ asset('img/Logo-rsud/logo-rsud.png') }}" alt="Logo RSUD" class="w-16 h-16 object-contain">
                                </div>
                            </div>
                        </div>
                        <div class="border-b border-black mb-4"></div>

                        <!-- JUDUL & NOMOR SURAT -->
                        <div class="text-center font-sans mb-3">
                            <h3 class="font-black text-xs sm:text-sm uppercase underline tracking-wider">BERITA ACARA PENYERAHAN BARANG</h3>
                            <p class="text-[11px] font-semibold">Nomor : <span x-text="selectedDistribusi.nomor_bast"></span></p>
                        </div>

                        <!-- PARAGRAF PEMBUKA -->
                        <p class="text-justify mb-2 leading-relaxed">
                            Pada hari ini <strong x-text="selectedDistribusi.hari || 'Kamis'"></strong> tanggal <strong x-text="selectedDistribusi.tanggal_angka || '13'"></strong> bulan <strong x-text="selectedDistribusi.bulan || 'Agustus'"></strong> tahun <strong x-text="selectedDistribusi.tahun || '2026'"></strong>, yang bertanda tangan di bawah ini :
                        </p>

                        <!-- IDENTITAS PIHAK PERTAMA (PENGURUS BARANG) -->
                        <div class="space-y-0.5 mb-2 ml-4 font-sans text-[10.5px]">
                            <div class="flex">
                                <div class="w-28 font-medium">Nama</div>
                                <div class="w-4">:</div>
                                <div class="flex-1 font-bold uppercase" x-text="selectedDistribusi.pengurus_nama"></div>
                            </div>
                            <div class="flex">
                                <div class="w-28 font-medium">NIP</div>
                                <div class="w-4">:</div>
                                <div class="flex-1 font-mono" x-text="selectedDistribusi.pengurus_nip"></div>
                            </div>
                            <div class="flex">
                                <div class="w-28 font-medium">Jabatan</div>
                                <div class="w-4">:</div>
                                <div class="flex-1" x-text="selectedDistribusi.pengurus_jabatan"></div>
                            </div>
                            <div class="flex">
                                <div class="w-28 font-medium">Ruangan</div>
                                <div class="w-4">:</div>
                                <div class="flex-1" x-text="selectedDistribusi.pengurus_ruangan || 'Gudang Perbekalan'"></div>
                            </div>
                        </div>

                        <!-- DASAR HUKUM SK BUPATI -->
                        <p class="text-justify mb-2 leading-relaxed">
                            Dalam hal ini selaku Pengurus Barang Aset Tahun Anggaran <span x-text="selectedDistribusi.tahun_anggaran || '2025'"></span> Rumah Sakit Umum Dr. H. Koesnandi Bondowoso, berdasarkan Surat Keputusan Bupati Kabupaten Bondowoso sesuai Nomor : <strong x-text="selectedDistribusi.sk_bupati_nomor || '188.45/969/430.4.2/2024'"></strong> tanggal <strong x-text="selectedDistribusi.sk_bupati_tanggal || '02 Januari 2025'"></strong>
                        </p>

                        <!-- IDENTITAS PIHAK KEDUA (PENERIMA / SUB ADMIN) -->
                        <p class="mb-1 leading-relaxed">Dengan ini menyerahkan barang kepada :</p>
                        <div class="space-y-0.5 mb-3 ml-4 font-sans text-[10.5px]">
                            <div class="flex">
                                <div class="w-28 font-medium">Nama</div>
                                <div class="w-4">:</div>
                                <div class="flex-1 font-bold uppercase" x-text="selectedDistribusi.pj_nama"></div>
                            </div>
                            <div class="flex">
                                <div class="w-28 font-medium">NIP</div>
                                <div class="w-4">:</div>
                                <div class="flex-1 font-mono" x-text="selectedDistribusi.pj_nip"></div>
                            </div>
                            <div class="flex">
                                <div class="w-28 font-medium">Jabatan</div>
                                <div class="w-4">:</div>
                                <div class="flex-1" x-text="selectedDistribusi.pj_jabatan"></div>
                            </div>
                            <div class="flex">
                                <div class="w-28 font-medium">Ruangan</div>
                                <div class="w-4">:</div>
                                <div class="flex-1 uppercase font-bold" x-text="selectedDistribusi.pj_ruangan || selectedDistribusi.unit_nama"></div>
                            </div>
                        </div>

                        <!-- TABEL RESMI DAFTAR BARANG YANG DISERAHKAN (7 KOLOM DENGAN SUB-KOLOM KONDISI) -->
                        <div class="my-3">
                            <table class="w-full text-center border-collapse border border-black text-[10px] font-sans">
                                <thead>
                                    <tr class="bg-gray-200 font-bold border-b border-black">
                                        <th rowspan="2" class="border border-black px-2 py-1.5 w-8">No</th>
                                        <th rowspan="2" class="border border-black px-3 py-1.5 text-left">Uraian Barang</th>
                                        <th rowspan="2" class="border border-black px-3 py-1.5 text-left">Merk /Type</th>
                                        <th rowspan="2" class="border border-black px-2 py-1.5 w-12">Vol</th>
                                        <th rowspan="2" class="border border-black px-2 py-1.5 w-14">Satuan</th>
                                        <th colspan="3" class="border border-black px-2 py-1">Kondisi</th>
                                        <th rowspan="2" class="border border-black px-3 py-1.5 text-left w-48">Keterangan</th>
                                    </tr>
                                    <tr class="bg-gray-200 font-bold border-b border-black text-[9px]">
                                        <th class="border border-black px-1.5 py-0.5 w-10">Baik</th>
                                        <th class="border border-black px-1.5 py-0.5 w-10">KB</th>
                                        <th class="border border-black px-1.5 py-0.5 w-10">Rusak</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(item, idx) in selectedDistribusi.items" :key="idx">
                                        <tr>
                                            <td class="border border-black px-2 py-1.5 font-mono text-center" x-text="idx + 1"></td>
                                            <td class="border border-black px-3 py-1.5 text-left font-semibold" x-text="item.nama_barang"></td>
                                            <td class="border border-black px-3 py-1.5 text-left text-[9.5px]" x-text="item.merk_type || item.spesifikasi"></td>
                                            <td class="border border-black px-2 py-1.5 font-mono font-bold text-center" x-text="item.qty"></td>
                                            <td class="border border-black px-2 py-1.5 text-center" x-text="item.satuan"></td>
                                            <td class="border border-black px-1.5 py-1.5 text-center font-bold" x-text="item.kondisi === 'Baik' ? 'Baik' : ''"></td>
                                            <td class="border border-black px-1.5 py-1.5 text-center font-bold" x-text="item.kondisi === 'KB' || item.kondisi === 'Kurang Baik' ? 'KB' : ''"></td>
                                            <td class="border border-black px-1.5 py-1.5 text-center font-bold" x-text="item.kondisi === 'Rusak' || item.kondisi === 'Rusak Berat' ? 'Rusak' : ''"></td>
                                            <td class="border border-black px-3 py-1.5 text-left text-[9px]" x-text="item.keterangan || selectedDistribusi.keterangan_lokasi"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <!-- KALIMAT PENUTUP -->
                        <p class="text-justify mb-4 leading-relaxed">
                            Demikian Berita Acara Penyerahan Barang ini dibuat rangkap secukupnya untuk dipergunakan sebagaimana mestinya.
                        </p>

                        <!-- AREA 2 TANDA TANGAN (KIRI: YANG MENYERAHKAN, KANAN: YANG MENERIMA) -->
                        <div class="grid grid-cols-2 gap-8 text-center font-sans text-[10px] mt-6">
                            <!-- Yang Menyerahkan -->
                            <div class="space-y-1">
                                <p class="font-normal">Yang Menyerahkan</p>
                                <p class="font-bold">Pengurus Barang Aset</p>
                                
                                <!-- Tanda Tangan Area -->
                                <div class="h-20 flex items-center justify-center py-1">
                                    <template x-if="selectedDistribusi.signed">
                                        <div class="flex items-center space-x-2 p-1.5 border border-emerald-600 bg-emerald-50 rounded">
                                            <div class="w-11 h-11 bg-white border border-black p-0.5 flex items-center justify-center">
                                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=BSRE-RSUD-KOESNANDI-PENGURUS-ASET" class="w-full h-full object-contain">
                                            </div>
                                            <div class="text-left text-[7.5px] leading-tight text-emerald-950 font-sans">
                                                <div class="font-bold">DITANDATANGANI ELEKTRONIK</div>
                                                <div>Pengurus Barang Aset RSUD</div>
                                                <div class="font-mono">Terverifikasi BSrE SIMAT</div>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!selectedDistribusi.signed">
                                        <div class="h-16 flex items-center justify-center text-slate-400 italic text-[10px]">
                                            ( Tanda Tangan )
                                        </div>
                                    </template>
                                </div>

                                <p class="font-bold underline text-[11px] uppercase tracking-wide" x-text="selectedDistribusi.pengurus_nama"></p>
                                <p class="font-mono text-[9.5px]" x-text="'NIP. ' + selectedDistribusi.pengurus_nip"></p>
                            </div>

                            <!-- Yang Menerima -->
                            <div class="space-y-1">
                                <p class="font-normal">Yang Menerima</p>
                                <p class="font-bold" x-text="selectedDistribusi.pj_jabatan_ttd || ('Kepala Ruangan ' + (selectedDistribusi.pj_ruangan || selectedDistribusi.unit_nama))"></p>
                                
                                <!-- Tanda Tangan Area -->
                                <div class="h-20 flex items-center justify-center py-1">
                                    <template x-if="selectedDistribusi.signed">
                                        <div class="flex items-center space-x-2 p-1.5 border border-teal-600 bg-teal-50 rounded">
                                            <div class="w-11 h-11 bg-white border border-black p-0.5 flex items-center justify-center">
                                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=BSRE-RSUD-KOESNANDI-DST-UNIT" class="w-full h-full object-contain">
                                            </div>
                                            <div class="text-left text-[7.5px] leading-tight text-teal-950 font-sans">
                                                <div class="font-bold">DITANDATANGANI ELEKTRONIK</div>
                                                <div>Penerima / Sub-Admin Ruangan</div>
                                                <div class="font-mono" x-text="selectedDistribusi.tgl_signed || '13/08/2026 11:30 WIB'"></div>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!selectedDistribusi.signed">
                                        <div class="h-16 flex items-center justify-center text-slate-400 italic text-[10px]">
                                            ( Tanda Tangan )
                                        </div>
                                    </template>
                                </div>

                                <p class="font-bold underline text-[11px] uppercase tracking-wide" x-text="selectedDistribusi.pj_nama"></p>
                                <p class="font-mono text-[9.5px]" x-text="'Nip. ' + selectedDistribusi.pj_nip"></p>
                            </div>
                        </div>

                    </div>
                </template>

            </div>
        </div>

    </div>
</x-layout>
