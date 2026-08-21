<x-layout title="Berita Acara (BAST) - SIMAT-RK">
    @section('page-title', 'Berita Acara (BAST)')
    @section('breadcrumb', 'Master Utama / Berita Acara (BAST)')

    <div x-data="{
        // Tab Navigasi Aktif: 'triwulan', 'distribusi', atau 'mutasi'
        activeTab: 'triwulan',
        
        // Modal Live Edit Toggle
        showEditTriwulanForm: false,
        showEditDistribusiForm: false,
        showEditMutasiForm: false,

        // =========================================================================
        // DATA TAB 1: BAST PENAMBAHAN DATA ASTAP BERDASARKAN TRIWULAN
        // =========================================================================
        selectedTahun: '2026',
        selectedTriwulanKey: 'TW2',
        showPrintTriwulanModal: false,
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
                pihak1_jabatan: 'Pejabat Pembuat Komitmen (PPK) / Penerima Hasil Pengadaan RSUD dr.H.Koesnandi',
                pihak2_nama: 'BUDI HARTONO,S.Sos',
                pihak2_nip: '19760229 200801 1 010',
                pihak2_jabatan: 'Pengurus Barang Aset Pada RSUD dr.H.Koesnandi Kabupaten Bondowoso',
                direktur_nama: 'dr. DIAN ARISANDI, M.Kes',
                direktur_nip: '19730514 200212 2 003',
                pihak2_signed: true,
                pihak2_tgl_ttd: '31/03/2026 15:40 WIB',
                pihak2_qr_hash: 'BSRE-KOESNANDI-BAST-TW1-2026-0914',
                status: 'Telah Ditandatangani BSrE',
                
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
                pihak1_jabatan: 'Pejabat Pembuat Komitmen (PPK) / Penerima Hasil Pengadaan RSUD dr.H.Koesnandi',
                pihak2_nama: 'BUDI HARTONO,S.Sos',
                pihak2_nip: '19760229 200801 1 010',
                pihak2_jabatan: 'Pengurus Barang Aset Pada RSUD dr.H.Koesnandi Kabupaten Bondowoso',
                direktur_nama: 'dr. DIAN ARISANDI, M.Kes',
                direktur_nip: '19730514 200212 2 003',
                pihak2_signed: true,
                pihak2_tgl_ttd: '30/06/2026 14:32 WIB',
                pihak2_qr_hash: 'BSRE-KOESNANDI-BAST-TW2-2026-0887',
                status: 'Telah Ditandatangani BSrE',

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
                pihak1_jabatan: 'Pejabat Pembuat Komitmen (PPK) / Penerima Hasil Pengadaan RSUD dr.H.Koesnandi',
                pihak2_nama: 'BUDI HARTONO,S.Sos',
                pihak2_nip: '19760229 200801 1 010',
                pihak2_jabatan: 'Pengurus Barang Aset Pada RSUD dr.H.Koesnandi Kabupaten Bondowoso',
                direktur_nama: 'dr. DIAN ARISANDI, M.Kes',
                direktur_nip: '19730514 200212 2 003',
                pihak2_signed: false,
                pihak2_tgl_ttd: '-',
                pihak2_qr_hash: '',
                status: 'Belum TTD',

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
                pihak1_jabatan: 'Pejabat Pembuat Komitmen (PPK) / Penerima Hasil Pengadaan RSUD dr.H.Koesnandi',
                pihak2_nama: 'BUDI HARTONO,S.Sos',
                pihak2_nip: '19760229 200801 1 010',
                pihak2_jabatan: 'Pengurus Barang Aset Pada RSUD dr.H.Koesnandi Kabupaten Bondowoso',
                direktur_nama: 'dr. DIAN ARISANDI, M.Kes',
                direktur_nip: '19730514 200212 2 003',
                pihak2_signed: false,
                pihak2_tgl_ttd: '-',
                pihak2_qr_hash: '',
                status: 'Belum TTD',
                rekapItems: [
                    { no: '1.', nama: 'Tanah', qty: 0, nilai: 0 },
                    { no: '2.', nama: 'Peralatan Dan Mesin', qty: 0, nilai: 0 },
                    { no: '3.', nama: 'Gedung Dan Bangunan', qty: 0, nilai: 0 },
                    { no: '4.', nama: 'Jalan, Irigasi Dan Jaringan', qty: 0, nilai: 0 },
                    { no: '5.', nama: 'Aset Tetap Lainnya', qty: 0, nilai: 0 },
                    { no: '6.', nama: 'Kontruksi Dalam Pengerjaan (KDP)', qty: 0, nilai: 0 },
                    { no: '7.', nama: 'Aset Tidak Berwujud (ATB)', qty: 0, nilai: 0 },
                    { no: '8.', nama: 'Exstra Comtable', qty: 0, nilai: 0 }
                ],
                detailBarang: []
            }
        },

        get currentTriwulanDoc() {
            return this.triwulanData[this.selectedTriwulanKey] || this.triwulanData['TW2'];
        },

        get currentTriwulanTotalNilai() {
            return this.currentTriwulanDoc.rekapItems.reduce((acc, item) => acc + item.nilai, 0);
        },

        get currentTriwulanTotalQty() {
            return this.currentTriwulanDoc.rekapItems.reduce((acc, item) => acc + item.qty, 0);
        },

        get filteredDetailBarangTriwulan() {
            const query = (this.searchBarangTriwulan || '').toLowerCase();
            return (this.currentTriwulanDoc.detailBarang || []).filter(b => {
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
        distribusiStatusFilter: 'all',
        showPrintDistribusiModal: false,
        showDetailDistribusiModal: false,
        selectedDistribusi: null,
        selectedDetailDistribusi: null,

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
                status: 'Telah Ditandatangani BSrE',
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
                status: 'Telah Ditandatangani BSrE',
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
                status: 'Belum TTD',
                keterangan_lokasi: 'Pemasangan & Testing oleh Tim Teknisi IPSRS',
                signed: false,
                tgl_signed: '-',
                qr_hash: '',
                items: [
                    { no: 1, nama_barang: 'Submersible Pump Franklin 7.5 HP', merk_type: 'Franklin Electric 4 Inch Super Stainless 3-Phase', qty: 1, satuan: 'Unit', kondisi: 'Baik', keterangan: 'Sumur Dalam Sentral Gedung Utama' }
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
                
                let matchStatus = true;
                if (this.distribusiStatusFilter === 'signed') {
                    matchStatus = d.signed === true;
                } else if (this.distribusiStatusFilter === 'unsigned') {
                    matchStatus = d.signed === false;
                }
                return matchQuery && matchUnit && matchStatus;
            });
        },

        openDetailDistribusi(item) {
            this.selectedDetailDistribusi = item;
            this.showDetailDistribusiModal = true;
        },

        // =========================================================================
        // DATA TAB 3: BAST MUTASI ASET (PEMINDAHAN ANTAR RUANGAN)
        // =========================================================================
        mutasiSearch: '',
        mutasiStatusFilter: 'all',
        showPrintMutasiModal: false,
        showDetailMutasiModal: false,
        selectedMutasi: null,
        selectedDetailMutasi: null,

        mutasiList: [
            {
                id: 1,
                nomor_bast: '034 / MTS / 430.10.7 / 2026',
                tgl_bast: 'Senin, 10 Agustus 2026',
                hari: 'Senin',
                tanggal_angka: '10',
                bulan: 'Agustus',
                tahun: '2026',
                tahun_anggaran: '2025',
                sk_bupati_nomor: '188.45/969/430.4.2/2024',
                sk_bupati_tanggal: '02 Januari 2025',
                nama: 'Bed Pasien Crank Manual (3 Unit)',
                kode_barang: '1.3.2.02.01.08.002',
                qty: 3,
                satuan: 'Unit',
                asal: 'Ruang Rawat Inap Melati',
                tujuan: 'Paviliun Graha Amukti',
                pemohon: 'dr. H. Rahmat, Sp.PD',
                pj_asal_nama: 'dr. H. Rahmat, Sp.PD',
                pj_asal_nip: '198004152006041008',
                pj_asal_jabatan: 'Kepala Ruangan Melati',
                pj_tujuan_nama: 'dr. ADHI SUDARMADJI',
                pj_tujuan_nip: '198410272009021003',
                pj_tujuan_jabatan: 'Kepala Ruangan Graha Amukti',
                status: 'Telah Ditandatangani BSrE',
                keterangan: 'Penambahan kapasitas ranjang cadangan ruang isolasi VIP',
                signed: true,
                tgl_signed: '10/08/2026 10:20 WIB',
                qr_hash: 'BSRE-KOESNANDI-MTS-2026-002'
            },
            {
                id: 2,
                nomor_bast: '038 / MTS / 430.10.7 / 2026',
                tgl_bast: 'Rabu, 12 Agustus 2026',
                hari: 'Rabu',
                tanggal_angka: '12',
                bulan: 'Agustus',
                tahun: '2026',
                tahun_anggaran: '2025',
                sk_bupati_nomor: '188.45/969/430.4.2/2024',
                sk_bupati_tanggal: '02 Januari 2025',
                nama: 'Infusion Pump Terumo TE-112',
                kode_barang: '1.3.2.02.01.01.012',
                qty: 2,
                satuan: 'Unit',
                asal: 'Instalasi Gawat Darurat (IGD)',
                tujuan: 'Ruang ICU Medis',
                pemohon: 'dr. Anita Wijaya, Sp.Em',
                pj_asal_nama: 'dr. Anita Wijaya, Sp.Em',
                pj_asal_nip: '198603122010012009',
                pj_asal_jabatan: 'Kepala Ruangan IGD',
                pj_tujuan_nama: 'dr. H. Syaiful, Sp.An',
                pj_tujuan_nip: '197911182005011004',
                pj_tujuan_jabatan: 'Kepala Ruangan ICU Medis',
                status: 'Telah Ditandatangani BSrE',
                keterangan: 'Kebutuhan mendesak monitoring cairan pasien kritis ICU',
                signed: true,
                tgl_signed: '12/08/2026 14:05 WIB',
                qr_hash: 'BSRE-KOESNANDI-MTS-2026-005'
            },
            {
                id: 3,
                nomor_bast: '041 / MTS / 430.10.7 / 2026',
                tgl_bast: 'Jumat, 14 Agustus 2026',
                hari: 'Jumat',
                tanggal_angka: '14',
                bulan: 'Agustus',
                tahun: '2026',
                tahun_anggaran: '2025',
                sk_bupati_nomor: '188.45/969/430.4.2/2024',
                sk_bupati_tanggal: '02 Januari 2025',
                nama: 'Komputer Desktop All-in-One Core i5',
                kode_barang: '1.3.2.10.01.02.003',
                qty: 1,
                satuan: 'Unit',
                asal: 'Gudang Inventaris Pusat',
                tujuan: 'Poliklinik Jantung Terpadu',
                pemohon: 'Ns. Bagus, S.Kep',
                pj_asal_nama: 'BUDI HARTONO, S.Sos',
                pj_asal_nip: '197602292008011010',
                pj_asal_jabatan: 'Pengurus Barang',
                pj_tujuan_nama: 'Ns. Bagus, S.Kep',
                pj_tujuan_nip: '199104052018021001',
                pj_tujuan_jabatan: 'Kepala Ruangan Poli Jantung',
                status: 'Belum TTD',
                keterangan: 'Penggantian PC lama unit entri resep elektronik',
                signed: false,
                tgl_signed: '-',
                qr_hash: ''
            }
        ],

        get filteredMutasiList() {
            const query = (this.mutasiSearch || '').toLowerCase();
            return this.mutasiList.filter(m => {
                const matchQuery = (m.nomor_bast || '').toLowerCase().includes(query) ||
                                   (m.nama || '').toLowerCase().includes(query) ||
                                   (m.asal || '').toLowerCase().includes(query) ||
                                   (m.tujuan || '').toLowerCase().includes(query) ||
                                   (m.pemohon || '').toLowerCase().includes(query);
                
                let matchStatus = true;
                if (this.mutasiStatusFilter === 'signed') {
                    matchStatus = m.signed === true;
                } else if (this.mutasiStatusFilter === 'unsigned') {
                    matchStatus = m.signed === false;
                }
                return matchQuery && matchStatus;
            });
        },

        openDetailMutasi(item) {
            this.selectedDetailMutasi = item;
            this.showDetailMutasiModal = true;
        },

        // Helper Format Rupiah & Angka
        formatRupiah(val) {
            return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(val || 0);
        },

        formatNumber(val) {
            return new Intl.NumberFormat('id-ID').format(val || 0);
        },

        // =========================================================================
        // LOGIK TOGGLE TANDA TANGAN DIGITAL BSR-E (TTD & BATALKAN TTD) 3 JENIS BAST
        // =========================================================================

        // 1. Toggle TTD BAST Triwulan
        openPrintTriwulan(twKey) {
            if (twKey) this.selectedTriwulanKey = twKey;
            this.showPrintTriwulanModal = true;
        },

        toggleSignTriwulan(key) {
            const doc = this.triwulanData[key || this.selectedTriwulanKey];
            if (doc) {
                if (doc.pihak2_signed) {
                    doc.pihak2_signed = false;
                    doc.pihak2_tgl_ttd = '-';
                    doc.pihak2_qr_hash = '';
                    doc.status = 'Belum TTD';
                    alert('↩️ Tanda tangan digital BSrE BAST Triwulan ' + doc.key + ' berhasil dibatalkan.');
                } else {
                    doc.pihak2_signed = true;
                    const now = new Date();
                    doc.pihak2_tgl_ttd = now.toLocaleDateString('id-ID') + ' ' + String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0') + ' WIB';
                    doc.pihak2_qr_hash = 'BSRE-KOESNANDI-TW-' + Date.now();
                    doc.status = 'Telah Ditandatangani BSrE';
                    alert('✍️ BAST Triwulan ' + doc.key + ' berhasil ditandatangani secara digital (QR Code BSrE Aktif)!');
                }
            }
        },

        // 2. Toggle TTD BAST Distribusi
        openPrintDistribusi(item) {
            this.selectedDistribusi = item ? { ...item } : this.distribusiList[0];
            this.showPrintDistribusiModal = true;
        },

        toggleSignDistribusi(item) {
            const target = item || this.selectedDistribusi;
            if (target) {
                if (target.signed) {
                    target.signed = false;
                    target.tgl_signed = '-';
                    target.qr_hash = '';
                    target.status = 'Belum TTD';
                    alert('↩️ Tanda tangan digital BSrE BAST Distribusi (' + target.nomor_bast + ') berhasil dibatalkan.');
                } else {
                    target.signed = true;
                    const now = new Date();
                    target.tgl_signed = now.toLocaleDateString('id-ID') + ' ' + String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0') + ' WIB';
                    target.qr_hash = 'BSRE-KOESNANDI-DST-' + Date.now();
                    target.status = 'Telah Ditandatangani BSrE';
                    alert('✍️ BAST Distribusi (' + target.nomor_bast + ') berhasil ditandatangani secara digital (QR Code BSrE Aktif)!');
                }
            }
        },

        // 3. Toggle TTD BAST Mutasi
        openPrintMutasi(item) {
            this.selectedMutasi = item ? { ...item } : this.mutasiList[0];
            this.showPrintMutasiModal = true;
        },

        toggleSignMutasi(item) {
            const target = item || this.selectedMutasi;
            if (target) {
                if (target.signed) {
                    target.signed = false;
                    target.tgl_signed = '-';
                    target.qr_hash = '';
                    target.status = 'Belum TTD';
                    alert('↩️ Tanda tangan digital BSrE BAST Mutasi (' + target.nomor_bast + ') berhasil dibatalkan.');
                } else {
                    target.signed = true;
                    const now = new Date();
                    target.tgl_signed = now.toLocaleDateString('id-ID') + ' ' + String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0') + ' WIB';
                    target.qr_hash = 'BSRE-KOESNANDI-MTS-' + Date.now();
                    target.status = 'Telah Ditandatangani BSrE';
                    alert('✍️ BAST Mutasi (' + target.nomor_bast + ') berhasil ditandatangani secara digital (QR Code BSrE Aktif)!');
                }
            }
        },

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
                        <span>MODUL PENGESAHAN BAST (LIVE EDIT, TTD BSR-E, & BATALKAN TTD)</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Pusat Cetak & Pengesahan BAST</h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                        Pengesahan Tanda Tangan Digital BSrE, Pembatalan TTD, Live Edit Dokumen Surat, dan Pencetakan 3 Jenis Berita Acara (Triwulan ASTAP, Distribusi Unit, & Mutasi Aset).
                    </p>
                </div>
                
                <!-- Quick Print Buttons (Tersusun Sejajar Rapi) -->
                <div class="flex flex-wrap lg:flex-nowrap items-center gap-2 sm:gap-3 shrink-0">
                    <button type="button" @click="activeTab = 'triwulan'; openPrintTriwulan('TW2')"
                        class="px-3.5 py-2.5 rounded-2xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-purple-500/20 transition-all flex items-center space-x-1.5 shrink-0 active:scale-95">
                        <span>🏛️ BAST Triwulan</span>
                    </button>
                    <button type="button" @click="activeTab = 'distribusi'; openPrintDistribusi(distribusiList[0])"
                        class="px-3.5 py-2.5 rounded-2xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-teal-500/20 transition-all flex items-center space-x-1.5 shrink-0 active:scale-95">
                        <span>🚚 BAST Distribusi</span>
                    </button>
                    <button type="button" @click="activeTab = 'mutasi'; openPrintMutasi(mutasiList[0])"
                        class="px-3.5 py-2.5 rounded-2xl bg-rose-500 hover:bg-rose-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-rose-500/20 transition-all flex items-center space-x-1.5 shrink-0 active:scale-95">
                        <span>🔄 BAST Mutasi</span>
                    </button>
                </div>
            </div>

            <!-- Mini Summary KPI Cards Strip -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-800/80">
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-purple-500/10 text-purple-400 text-lg">🏛️</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">BAST Triwulan ASTAP</span>
                        <span class="text-sm sm:text-base font-extrabold text-white">4 Periode Rekap</span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-teal-500/10 text-teal-400 text-lg">🚚</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">BAST Distribusi Unit</span>
                        <span class="text-sm sm:text-base font-extrabold text-teal-300" x-text="distribusiList.length + ' Transaksi'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-rose-500/10 text-rose-400 text-lg">🔄</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">BAST Mutasi Aset</span>
                        <span class="text-sm sm:text-base font-extrabold text-rose-300" x-text="mutasiList.length + ' Transaksi'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-lg">✍️</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Status TTD BSrE</span>
                        <span class="text-sm sm:text-base font-extrabold text-emerald-300">TTD & Batal TTD</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- NAVIGATION 3 TABS: TAB 1 (TRIWULAN) | TAB 2 (DISTRIBUSI) | TAB 3 (MUTASI)  -->
        <!-- ========================================================================= -->
        <div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl p-3 shadow-xl mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                
                <!-- Tab 1 Button -->
                <button type="button" @click="activeTab = 'triwulan'"
                    class="p-4 rounded-2xl transition-all flex items-center space-x-3 text-left"
                    :class="activeTab === 'triwulan' ? 'bg-purple-500/20 text-purple-300 border-2 border-purple-500/50 shadow-lg shadow-purple-500/10' : 'bg-slate-950/60 text-slate-400 hover:text-white border border-slate-800/80'">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg font-bold shrink-0"
                         :class="activeTab === 'triwulan' ? 'bg-purple-500 text-slate-950 shadow-md shadow-purple-500/30' : 'bg-slate-900 text-slate-400 border border-slate-800'">
                        <span>🏛️</span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] font-bold uppercase tracking-wider block" :class="activeTab === 'triwulan' ? 'text-purple-400' : 'text-slate-500'">Pengadaan ASTAP</span>
                        <span class="text-xs sm:text-sm font-extrabold block text-white truncate">1. BAST Triwulan ASTAP</span>
                        <span class="text-[10px] text-slate-400 block truncate">Rekap 8 Kategori & SPK Perolehan</span>
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
                        <span class="text-[10px] font-bold uppercase tracking-wider block" :class="activeTab === 'distribusi' ? 'text-teal-400' : 'text-slate-500'">Penyerahan Unit</span>
                        <span class="text-xs sm:text-sm font-extrabold block text-white truncate">2. BAST Distribusi Aset</span>
                        <span class="text-[10px] text-slate-400 block truncate">Serah Terima Sekali Transaksi Unit</span>
                    </div>
                </button>

                <!-- Tab 3 Button -->
                <button type="button" @click="activeTab = 'mutasi'"
                    class="p-4 rounded-2xl transition-all flex items-center space-x-3 text-left"
                    :class="activeTab === 'mutasi' ? 'bg-rose-500/20 text-rose-300 border-2 border-rose-500/50 shadow-lg shadow-rose-500/10' : 'bg-slate-950/60 text-slate-400 hover:text-white border border-slate-800/80'">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg font-bold shrink-0"
                         :class="activeTab === 'mutasi' ? 'bg-rose-500 text-slate-950 shadow-md shadow-rose-500/30' : 'bg-slate-900 text-slate-400 border border-slate-800'">
                        <span>🔄</span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] font-bold uppercase tracking-wider block" :class="activeTab === 'mutasi' ? 'text-rose-400' : 'text-slate-500'">Pemindahan Ruangan</span>
                        <span class="text-xs sm:text-sm font-extrabold block text-white truncate">3. BAST Mutasi Aset</span>
                        <span class="text-[10px] text-slate-400 block truncate">Pemindahan Barang Ruang Asal &rarr; Tujuan</span>
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

                    <!-- Tahun Dropdown & Tombol Aksi TTD / Cetak -->
                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <select x-model="selectedTahun" class="bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs font-bold text-white focus:outline-none focus:border-purple-500">
                            <option value="2026">Tahun Anggaran 2026</option>
                            <option value="2025">Tahun Anggaran 2025</option>
                        </select>

                        <!-- Button Toggle TTD BSrE / Batalkan TTD -->
                        <button type="button" @click="toggleSignTriwulan(selectedTriwulanKey)"
                            :class="currentTriwulanDoc.pihak2_signed ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40 hover:bg-rose-500/30' : 'bg-emerald-500 hover:bg-emerald-400 text-slate-950 shadow-lg shadow-emerald-500/20'"
                            class="px-3.5 py-2 rounded-xl font-extrabold text-xs transition-all active:scale-95 flex items-center space-x-1">
                            <span x-text="currentTriwulanDoc.pihak2_signed ? '↩️ Batalkan TTD BSrE' : '✍️ TTD BSrE'"></span>
                        </button>

                        <!-- Button Cetak BAST Triwulan -->
                        <button type="button" @click="openPrintTriwulan(selectedTriwulanKey)"
                            class="px-3.5 py-2 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-purple-500/20 transition-all flex items-center space-x-1 active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>🖨️ Cetak / Edit</span>
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
                            <span x-text="currentTriwulanDoc.pihak2_signed ? '✍️ Ditandatangani BSrE' : '⏳ Belum TTD'"></span>
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
                            <input type="text" x-model="distribusiSearch" placeholder="Cari nomor BAST / unit / PJ..."
                                class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-2.5 pl-10 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-teal-500">
                            <svg class="w-4 h-4 text-teal-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <!-- Filter Status TTD -->
                        <select x-model="distribusiStatusFilter" class="bg-slate-950 border border-slate-800 rounded-2xl px-3 py-2.5 text-xs text-teal-300 font-bold focus:outline-none focus:border-teal-500">
                            <option value="all">🔍 Semua Status TTD</option>
                            <option value="signed">✍️ Telah Ditandatangani BSrE</option>
                            <option value="unsigned">⏳ Belum Ditandatangani</option>
                        </select>

                        <!-- Filter Unit Dropdown -->
                        <select x-model="distribusiUnitFilter" class="bg-slate-950 border border-slate-800 rounded-2xl px-3 py-2.5 text-xs text-slate-300 focus:outline-none focus:border-teal-500">
                            <option value="all">Semua Unit / Paviliun (Sub-Admin)</option>
                            <option value="Front Office (FO) & Rawat Inap">Front Office (FO) & Rawat Inap</option>
                            <option value="Instalasi Gawat Darurat (IGD)">Instalasi Gawat Darurat (IGD)</option>
                            <option value="Instalasi Pemeliharaan Sarana RS (IPSRS)">Instalasi IPSRS</option>
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
                            <th class="px-4 py-3.5 text-center">Status TTD BSrE</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap">Aksi BAST</th>
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
                                        <span x-text="item.signed ? '✍️ Ditandatangani BSrE' : '⏳ Belum TTD'"></span>
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center space-x-1.5 whitespace-nowrap">
                                    
                                    <!-- 1. Tombol Toggle TTD / Batalkan TTD -->
                                    <button type="button" @click="toggleSignDistribusi(item)"
                                        :class="item.signed ? 'bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/40' : 'bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold shadow-sm'"
                                        class="px-2.5 py-1.5 rounded-xl font-bold text-xs transition-all inline-flex items-center space-x-1 active:scale-95"
                                        :title="item.signed ? 'Batalkan Tanda Tangan Digital BSrE' : 'Tanda Tangan Digital BSrE'">
                                        <span x-text="item.signed ? '↩️ Batal TTD' : '✍️ TTD BSrE'"></span>
                                    </button>

                                    <!-- 2. Tombol Rincian Modal -->
                                    <button type="button" @click="openDetailDistribusi(item)"
                                        class="px-2.5 py-1.5 rounded-xl bg-teal-500/15 hover:bg-teal-500/25 text-teal-300 border border-teal-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95">
                                        <span>👁️ Rincian</span>
                                    </button>

                                    <!-- 3. Tombol Cetak (dengan Live Edit Panel) -->
                                    <button type="button" @click="openPrintDistribusi(item)"
                                        class="px-2.5 py-1.5 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95">
                                        <span>🖨️ Cetak / Edit</span>
                                    </button>

                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- KONTEN TAB 3: BAST MUTASI ASET (PEMINDAHAN ANTAR RUANGAN)                  -->
        <!-- ========================================================================= -->
        <div x-show="activeTab === 'mutasi'" class="space-y-6" x-cloak>
            
            <!-- Toolbar & Filter Status Mutasi -->
            <div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    
                    <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                        <div class="relative flex-1 sm:w-80">
                            <input type="text" x-model="mutasiSearch" placeholder="Cari nomor BAST mutasi / barang / ruangan..."
                                class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-2.5 pl-10 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500">
                            <svg class="w-4 h-4 text-rose-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <!-- Filter Status TTD -->
                        <select x-model="mutasiStatusFilter" class="bg-slate-950 border border-slate-800 rounded-2xl px-3 py-2.5 text-xs text-rose-300 font-bold focus:outline-none focus:border-rose-500">
                            <option value="all">🔍 Semua Status TTD Mutasi</option>
                            <option value="signed">✍️ Telah Ditandatangani BSrE</option>
                            <option value="unsigned">⏳ Belum Ditandatangani</option>
                        </select>
                    </div>

                    <a href="{{ route('mutasi.create') }}"
                        class="px-4 py-2.5 rounded-xl bg-rose-500 hover:bg-rose-400 text-slate-950 font-bold text-xs shadow-lg shadow-rose-500/20 transition-all flex items-center space-x-2 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>+ Input Mutasi Baru</span>
                    </a>

                </div>
            </div>

            <!-- Tabel Daftar BAST Mutasi Aset -->
            <div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6 overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="px-4 py-3.5 text-center w-12">No</th>
                            <th class="px-4 py-3.5 text-left">Nomor BAST Mutasi</th>
                            <th class="px-4 py-3.5 text-left">Nama Barang Dimutasi</th>
                            <th class="px-4 py-3.5 text-center">Ruangan Asal</th>
                            <th class="px-4 py-3.5 text-center">Ruangan Tujuan</th>
                            <th class="px-4 py-3.5 text-center">Status TTD BSrE</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap">Aksi BAST</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        <template x-for="(item, index) in filteredMutasiList" :key="item.id">
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-4 text-center font-bold text-slate-400" x-text="index + 1"></td>
                                <td class="px-4 py-4">
                                    <div class="font-mono font-bold text-rose-400" x-text="item.nomor_bast"></div>
                                    <div class="text-[10px] text-slate-400" x-text="item.tgl_bast"></div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="font-bold text-white" x-text="item.nama"></div>
                                    <div class="text-[10px] text-slate-400 font-mono" x-text="item.kode_barang + ' • Vol: ' + item.qty + ' ' + item.satuan"></div>
                                </td>
                                <td class="px-4 py-4 text-center font-semibold text-slate-300" x-text="item.asal"></td>
                                <td class="px-4 py-4 text-center font-semibold text-rose-300" x-text="item.tujuan"></td>
                                <td class="px-4 py-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border inline-flex items-center space-x-1"
                                          :class="item.signed ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border-amber-500/30'">
                                        <span x-text="item.signed ? '✍️ Ditandatangani BSrE' : '⏳ Belum TTD'"></span>
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center space-x-1.5 whitespace-nowrap">
                                    
                                    <!-- 1. Tombol Toggle TTD / Batalkan TTD -->
                                    <button type="button" @click="toggleSignMutasi(item)"
                                        :class="item.signed ? 'bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/40' : 'bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold shadow-sm'"
                                        class="px-2.5 py-1.5 rounded-xl font-bold text-xs transition-all inline-flex items-center space-x-1 active:scale-95"
                                        :title="item.signed ? 'Batalkan Tanda Tangan Digital BSrE' : 'Tanda Tangan Digital BSrE'">
                                        <span x-text="item.signed ? '↩️ Batal TTD' : '✍️ TTD BSrE'"></span>
                                    </button>

                                    <!-- 2. Tombol Rincian Modal -->
                                    <button type="button" @click="openDetailMutasi(item)"
                                        class="px-2.5 py-1.5 rounded-xl bg-rose-500/15 hover:bg-rose-500/25 text-rose-300 border border-rose-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95">
                                        <span>👁️ Rincian</span>
                                    </button>

                                    <!-- 3. Tombol Cetak (dengan Live Edit Panel) -->
                                    <button type="button" @click="openPrintMutasi(item)"
                                        class="px-2.5 py-1.5 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95">
                                        <span>🖨️ Cetak / Edit</span>
                                    </button>

                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- MODAL RINCIAN 1: DETAIL POPUP BAST DISTRIBUSI BARANG ASET                  -->
        <!-- ========================================================================= -->
        <div x-show="showDetailDistribusiModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-4 overflow-y-auto" x-cloak>
            <div @click.away="showDetailDistribusiModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-3xl w-full p-6 shadow-2xl space-y-5 my-auto relative">
                
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 rounded-2xl bg-teal-500/20 text-teal-300 border border-teal-500/30 text-xl font-bold">
                            🚚
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-teal-400 uppercase tracking-wider block">Rincian BAST Distribusi</span>
                            <h3 class="text-lg font-extrabold text-white" x-text="selectedDetailDistribusi ? selectedDetailDistribusi.nomor_bast : ''"></h3>
                        </div>
                    </div>
                    
                    <button type="button" @click="showDetailDistribusiModal = false" class="p-2 rounded-xl bg-slate-800 text-slate-400 hover:text-white font-bold">&times;</button>
                </div>

                <template x-if="selectedDetailDistribusi">
                    <div class="space-y-4 text-xs">
                        
                        <!-- Status Bar TTD BSrE & Toggle Button -->
                        <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <span class="text-[10px] text-slate-400 font-semibold block uppercase">Status Tanda Tangan Digital BSrE:</span>
                                <span class="text-sm font-extrabold"
                                      :class="selectedDetailDistribusi.signed ? 'text-emerald-400' : 'text-amber-400'"
                                      x-text="selectedDetailDistribusi.signed ? '✍️ Ditandatangani BSrE' : '⏳ Belum Ditandatangani'"></span>
                            </div>

                            <button type="button" @click="toggleSignDistribusi(selectedDetailDistribusi)"
                                :class="selectedDetailDistribusi.signed ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40 hover:bg-rose-500/30' : 'bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold shadow-md'"
                                class="px-4 py-2 rounded-xl font-extrabold text-xs transition-all active:scale-95">
                                <span x-text="selectedDetailDistribusi.signed ? '↩️ Batalkan TTD BSrE' : '✍️ Tandatangani Digital BSrE'"></span>
                            </button>
                        </div>

                        <!-- Data Informasi Unit & PJ -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-1">
                                <span class="text-[10px] text-teal-400 font-bold uppercase block">Unit / Paviliun Penerima</span>
                                <div class="font-extrabold text-white text-sm" x-text="selectedDetailDistribusi.unit_nama"></div>
                                <div class="text-[11px] text-slate-400" x-text="selectedDetailDistribusi.unit_tipe"></div>
                                <div class="text-[11px] text-slate-300 pt-1" x-text="'Catatan: ' + selectedDetailDistribusi.keterangan_lokasi"></div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-1">
                                <span class="text-[10px] text-emerald-400 font-bold uppercase block">Penanggung Jawab (Sub-Admin)</span>
                                <div class="font-extrabold text-emerald-300 text-sm" x-text="selectedDetailDistribusi.pj_nama"></div>
                                <div class="text-[11px] text-slate-400 font-mono" x-text="'NIP. ' + selectedDetailDistribusi.pj_nip"></div>
                                <div class="text-[11px] text-slate-300 pt-1" x-text="selectedDetailDistribusi.pj_jabatan_ttd"></div>
                            </div>
                        </div>

                        <!-- Tabel Item Barang -->
                        <div>
                            <span class="text-xs font-bold text-slate-300 uppercase tracking-wider block mb-2">📦 Rincian Barang Yang Penyerahannya Diberikan:</span>
                            <div class="overflow-x-auto rounded-2xl border border-slate-800">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-950 text-slate-400 font-bold border-b border-slate-800">
                                        <tr>
                                            <th class="px-3 py-2 text-center w-8">No</th>
                                            <th class="px-3 py-2">Nama Barang</th>
                                            <th class="px-3 py-2">Merk / Spesifikasi</th>
                                            <th class="px-3 py-2 text-center">Qty</th>
                                            <th class="px-3 py-2 text-center">Kondisi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800 bg-slate-900/60">
                                        <template x-for="(sub, idx) in selectedDetailDistribusi.items" :key="idx">
                                            <tr>
                                                <td class="px-3 py-2 text-center text-slate-400 font-mono" x-text="idx + 1"></td>
                                                <td class="px-3 py-2 font-bold text-white" x-text="sub.nama_barang"></td>
                                                <td class="px-3 py-2 text-slate-400" x-text="sub.merk_type"></td>
                                                <td class="px-3 py-2 text-center font-bold text-teal-300 font-mono" x-text="sub.qty + ' ' + sub.satuan"></td>
                                                <td class="px-3 py-2 text-center font-bold text-emerald-400" x-text="sub.kondisi"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-800 flex justify-between items-center">
                            <button type="button" @click="showDetailDistribusiModal = false; openPrintDistribusi(selectedDetailDistribusi)"
                                class="px-4 py-2 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-extrabold text-xs shadow-lg transition-all flex items-center space-x-1.5">
                                <span>🖨️ Cetak & Edit Surat BAST</span>
                            </button>
                            <button type="button" @click="showDetailDistribusiModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs">Tutup</button>
                        </div>

                    </div>
                </template>

            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL RINCIAN 2: DETAIL POPUP BAST MUTASI ASET                            -->
        <!-- ========================================================================= -->
        <div x-show="showDetailMutasiModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-4 overflow-y-auto" x-cloak>
            <div @click.away="showDetailMutasiModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-3xl w-full p-6 shadow-2xl space-y-5 my-auto relative">
                
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 rounded-2xl bg-rose-500/20 text-rose-300 border border-rose-500/30 text-xl font-bold">
                            🔄
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-rose-400 uppercase tracking-wider block">Rincian BAST Mutasi Aset</span>
                            <h3 class="text-lg font-extrabold text-white" x-text="selectedDetailMutasi ? selectedDetailMutasi.nomor_bast : ''"></h3>
                        </div>
                    </div>
                    
                    <button type="button" @click="showDetailMutasiModal = false" class="p-2 rounded-xl bg-slate-800 text-slate-400 hover:text-white font-bold">&times;</button>
                </div>

                <template x-if="selectedDetailMutasi">
                    <div class="space-y-4 text-xs">
                        
                        <!-- Status Bar TTD BSrE & Toggle Button -->
                        <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <span class="text-[10px] text-slate-400 font-semibold block uppercase">Status Tanda Tangan Digital BSrE:</span>
                                <span class="text-sm font-extrabold"
                                      :class="selectedDetailMutasi.signed ? 'text-emerald-400' : 'text-amber-400'"
                                      x-text="selectedDetailMutasi.signed ? '✍️ Ditandatangani BSrE' : '⏳ Belum Ditandatangani'"></span>
                            </div>

                            <button type="button" @click="toggleSignMutasi(selectedDetailMutasi)"
                                :class="selectedDetailMutasi.signed ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40 hover:bg-rose-500/30' : 'bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold shadow-md'"
                                class="px-4 py-2 rounded-xl font-extrabold text-xs transition-all active:scale-95">
                                <span x-text="selectedDetailMutasi.signed ? '↩️ Batalkan TTD BSrE' : '✍️ Tandatangani Digital BSrE'"></span>
                            </button>
                        </div>

                        <!-- Data Informasi Mutasi Ruangan -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-1">
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">Ruangan Asal</span>
                                <div class="font-extrabold text-white text-sm" x-text="selectedDetailMutasi.asal"></div>
                                <div class="text-[11px] text-purple-300" x-text="'PJ: ' + selectedDetailMutasi.pj_asal_nama"></div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-1">
                                <span class="text-[10px] text-rose-400 font-bold uppercase block">Ruangan Tujuan</span>
                                <div class="font-extrabold text-rose-300 text-sm" x-text="selectedDetailMutasi.tujuan"></div>
                                <div class="text-[11px] text-emerald-300" x-text="'PJ: ' + selectedDetailMutasi.pj_tujuan_nama"></div>
                            </div>
                        </div>

                        <!-- Info Barang Dimutasi -->
                        <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-2">
                            <span class="text-[10px] text-cyan-400 font-bold uppercase block">Barang Aset Yang Dimutasi</span>
                            <div class="font-extrabold text-white text-base" x-text="selectedDetailMutasi.nama"></div>
                            <div class="flex flex-wrap gap-3 font-mono text-[11px] text-slate-400">
                                <span>Kode 108: <strong class="text-cyan-300" x-text="selectedDetailMutasi.kode_barang"></strong></span>
                                <span>Volume: <strong class="text-white" x-text="selectedDetailMutasi.qty + ' ' + selectedDetailMutasi.satuan"></strong></span>
                            </div>
                            <div class="text-slate-300 text-[11px] pt-1" x-text="'Alasan Pemindahan: ' + selectedDetailMutasi.keterangan"></div>
                        </div>

                        <div class="pt-3 border-t border-slate-800 flex justify-between items-center">
                            <button type="button" @click="showDetailMutasiModal = false; openPrintMutasi(selectedDetailMutasi)"
                                class="px-4 py-2 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-extrabold text-xs shadow-lg transition-all flex items-center space-x-1.5">
                                <span>🖨️ Cetak & Edit Surat BAST</span>
                            </button>
                            <button type="button" @click="showDetailMutasiModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs">Tutup</button>
                        </div>

                    </div>
                </template>

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

                    <div class="flex items-center space-x-2 shrink-0">
                        <button type="button" @click="showEditTriwulanForm = !showEditTriwulanForm"
                            class="px-3.5 py-1.5 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 font-bold text-xs transition-all">
                            <span x-text="showEditTriwulanForm ? '✕ Tutup Form Edit' : '✏️ Edit Data & Pejabat BAST'"></span>
                        </button>

                        <!-- Toggle Button TTD / Batalkan TTD -->
                        <button type="button" @click="toggleSignTriwulan(selectedTriwulanKey)"
                            :class="currentTriwulanDoc.pihak2_signed ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40 hover:bg-rose-500/30' : 'bg-emerald-500 text-slate-950 font-extrabold shadow-md'"
                            class="px-3.5 py-1.5 rounded-xl font-bold text-xs transition-all active:scale-95">
                            <span x-text="currentTriwulanDoc.pihak2_signed ? '↩️ Batalkan TTD BSrE' : '✍️ TTD BSrE'"></span>
                        </button>

                        <button type="button" @click="printCurrent()" class="px-4 py-1.5 rounded-xl bg-purple-500 text-slate-950 font-bold text-xs shadow-lg">
                            🖨️ Cetak Surat
                        </button>
                        <button type="button" @click="showPrintTriwulanModal = false" class="p-1 rounded-lg text-slate-400 hover:text-white font-bold text-lg">&times;</button>
                    </div>
                </div>

                <!-- Formulir Edit Live BAST Triwulan -->
                <div x-show="showEditTriwulanForm" class="no-print bg-slate-950 p-4 rounded-2xl border border-purple-500/40 text-xs space-y-3 shadow-inner">
                    <div class="font-bold text-purple-300 text-[11px] uppercase tracking-wider border-b border-slate-800 pb-2">
                        ✏️ Live Edit Surat BAST Triwulan (Otomatis Berubah Pada Lembar Cetak):
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Nomor Surat BAST</label>
                            <input type="text" x-model="currentTriwulanDoc.nomor_surat" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-purple-300 font-mono font-bold text-xs">
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Hari & Tanggal Surat</label>
                            <input type="text" x-model="currentTriwulanDoc.hari_tanggal" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Lokasi Pelaksanaan</label>
                            <input type="text" x-model="currentTriwulanDoc.lokasi" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-800">
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Nama Pihak I (Menyerahkan / PPK)</label>
                            <input type="text" x-model="currentTriwulanDoc.pihak1_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white font-bold text-xs">
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Nama Pihak II (Menerima / Pengurus Barang)</label>
                            <input type="text" x-model="currentTriwulanDoc.pihak2_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-emerald-300 font-bold text-xs">
                        </div>
                    </div>
                </div>

                <!-- LEMBAR CETAK DOKUMEN TRIWULAN (F4/A4 PRINTABLE) -->
                <div class="bg-white text-black p-8 rounded-2xl font-serif shadow-2xl text-xs space-y-4 print:p-0 print:shadow-none">
                    
                    <div class="border-b-[3px] border-black pb-1 mb-0.5">
                        <div class="flex items-center justify-between gap-4">
                            <div class="w-20 shrink-0 flex justify-center">
                                <img src="{{ asset('img/Logo-rsud/logo-rsud.png') }}" alt="Logo RSUD" class="w-16 h-16 object-contain">
                            </div>
                            <div class="flex-1 text-center font-sans text-black">
                                <h4 class="font-bold text-xs sm:text-sm uppercase tracking-wide leading-tight">PEMERINTAH KABUPATEN BONDOWOSO</h4>
                                <h3 class="font-black text-sm sm:text-base uppercase tracking-tight leading-tight">RUMAH SAKIT UMUM DAERAH dr. H. KOESNADI</h3>
                                <p class="text-[10px] leading-tight mt-0.5">Jl. Kapten Pierre Tendean No. 3 Telepon (0332) 421974. Fax.0332 422311</p>
                                <p class="text-[10px] leading-tight">Website: rsudrkoesnadi.go.id, Email: rsu.koesnadi@gmail.com</p>
                            </div>
                            <div class="w-20 shrink-0 flex justify-center">
                                <img src="{{ asset('img/Logo-rsud/logo-rsud.png') }}" alt="Logo RSUD" class="w-16 h-16 object-contain">
                            </div>
                        </div>
                    </div>
                    <div class="border-b border-black mb-4"></div>

                    <div class="text-center font-sans mb-3">
                        <h3 class="font-black text-xs sm:text-sm uppercase underline tracking-wider">BERITA ACARA PENAMBAHAN ASET TETAP (ASTAP)</h3>
                        <p class="text-[11px] font-semibold">Nomor : <span x-text="currentTriwulanDoc.nomor_surat"></span></p>
                    </div>

                    <p class="text-justify mb-2 leading-relaxed font-sans">
                        Pada hari ini <strong x-text="currentTriwulanDoc.hari_tanggal"></strong> bertempat di <span x-text="currentTriwulanDoc.lokasi"></span>, kami yang bertanda tangan di bawah ini:
                    </p>

                    <div class="space-y-0.5 ml-4 font-sans text-[10.5px]">
                        <div class="flex"><div class="w-32 font-medium">Nama (Pihak I)</div><div class="w-4">:</div><div class="flex-1 font-bold uppercase" x-text="currentTriwulanDoc.pihak1_nama"></div></div>
                        <div class="flex"><div class="w-32 font-medium">NIP</div><div class="w-4">:</div><div class="flex-1 font-mono" x-text="currentTriwulanDoc.pihak1_nip"></div></div>
                        <div class="flex"><div class="w-32 font-medium">Jabatan</div><div class="w-4">:</div><div class="flex-1" x-text="currentTriwulanDoc.pihak1_jabatan"></div></div>
                    </div>

                    <p class="mt-2 mb-1 font-sans">Dengan ini menyerahkan penginputan data hasil pengadaan barang aset kepada:</p>
                    
                    <div class="space-y-0.5 ml-4 font-sans text-[10.5px]">
                        <div class="flex"><div class="w-32 font-medium">Nama (Pihak II)</div><div class="w-4">:</div><div class="flex-1 font-bold uppercase" x-text="currentTriwulanDoc.pihak2_nama"></div></div>
                        <div class="flex"><div class="w-32 font-medium">NIP</div><div class="w-4">:</div><div class="flex-1 font-mono" x-text="currentTriwulanDoc.pihak2_nip"></div></div>
                        <div class="flex"><div class="w-32 font-medium">Jabatan</div><div class="w-4">:</div><div class="flex-1" x-text="currentTriwulanDoc.pihak2_jabatan"></div></div>
                    </div>

                    <!-- TABEL REKAP TRIWULAN -->
                    <div class="my-3">
                        <table class="w-full text-center border-collapse border border-black text-[10px] font-sans">
                            <thead>
                                <tr class="bg-gray-200 font-bold border-b border-black">
                                    <th class="border border-black px-2 py-1.5 w-8">No</th>
                                    <th class="border border-black px-3 py-1.5 text-left">Kelompok / Kategori Aset</th>
                                    <th class="border border-black px-3 py-1.5 w-24">Jumlah Barang</th>
                                    <th class="border border-black px-4 py-1.5 text-right w-40">Nilai Realisasi Perolehan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="item in currentTriwulanDoc.rekapItems" :key="item.no">
                                    <tr class="border-b border-black">
                                        <td class="border border-black px-2 py-1" x-text="item.no"></td>
                                        <td class="border border-black px-3 py-1 text-left" x-text="item.nama"></td>
                                        <td class="border border-black px-3 py-1 font-bold" x-text="item.qty + ' Unit'"></td>
                                        <td class="border border-black px-4 py-1 text-right font-mono font-bold" x-text="'Rp ' + formatRupiah(item.nilai)"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <!-- TTD DUAL BSR-E -->
                    <div class="grid grid-cols-2 gap-8 text-center font-sans text-[10px] mt-6">
                        <div>
                            <p>Yang Menyerahkan (Pihak I)</p>
                            <p class="font-bold">Pejabat Pembuat Komitmen (PPK)</p>
                            <div class="h-16 flex items-center justify-center py-1">
                                <div class="p-1 border border-purple-600 bg-purple-50 rounded flex items-center space-x-1.5">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=BSRE-PPK-KOESNANDI" class="w-10 h-10">
                                    <div class="text-[7.5px] text-left leading-tight">
                                        <div class="font-bold">DITANDATANGANI ELEKTRONIK</div>
                                        <div>PPK RSUD dr. H. Koesnandi</div>
                                    </div>
                                </div>
                            </div>
                            <p class="font-bold underline uppercase" x-text="currentTriwulanDoc.pihak1_nama"></p>
                            <p class="font-mono text-[9px]" x-text="'NIP. ' + currentTriwulanDoc.pihak1_nip"></p>
                        </div>

                        <div>
                            <p>Yang Menerima (Pihak II)</p>
                            <p class="font-bold">Pengurus Barang Aset</p>
                            <div class="h-16 flex items-center justify-center py-1">
                                <template x-if="currentTriwulanDoc.pihak2_signed">
                                    <div class="p-1 border border-emerald-600 bg-emerald-50 rounded flex items-center space-x-1.5">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=BSRE-PENGURUS-KOESNANDI" class="w-10 h-10">
                                        <div class="text-[7.5px] text-left leading-tight">
                                            <div class="font-bold">DITANDATANGANI ELEKTRONIK</div>
                                            <div>Pengurus Barang Aset</div>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!currentTriwulanDoc.pihak2_signed">
                                    <span class="text-amber-600 font-bold italic text-[10px]">( Belum Ditandatangani BSrE )</span>
                                </template>
                            </div>
                            <p class="font-bold underline uppercase" x-text="currentTriwulanDoc.pihak2_nama"></p>
                            <p class="font-mono text-[9px]" x-text="'NIP. ' + currentTriwulanDoc.pihak2_nip"></p>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL CETAK 2: LEMBAR DOKUMEN BAST DISTRIBUSI BARANG ASET                 -->
        <!-- ========================================================================= -->
        <div x-show="showPrintDistribusiModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-2 sm:p-4 overflow-y-auto" x-cloak>
            <div @click.away="showPrintDistribusiModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-4xl w-full p-4 sm:p-6 shadow-2xl space-y-4 my-auto relative">
                
                <div class="no-print flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
                    <div class="flex items-center space-x-2">
                        <span class="p-2 rounded-xl bg-teal-500/20 text-teal-300 text-sm">🚚</span>
                        <div>
                            <h3 class="text-base font-extrabold text-white">Berita Acara Serah Terima Distribusi Barang</h3>
                            <p class="text-[11px] text-slate-400" x-text="selectedDistribusi ? ('Nomor BAST: ' + selectedDistribusi.nomor_bast) : ''"></p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <button type="button" @click="showEditDistribusiForm = !showEditDistribusiForm"
                            class="px-3.5 py-1.5 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 font-bold text-xs transition-all">
                            <span x-text="showEditDistribusiForm ? '✕ Tutup Form Edit' : '✏️ Edit Data & Pejabat BAST'"></span>
                        </button>

                        <!-- Toggle Button TTD / Batalkan TTD -->
                        <button type="button" @click="toggleSignDistribusi(selectedDistribusi)"
                            :class="selectedDistribusi && selectedDistribusi.signed ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40 hover:bg-rose-500/30' : 'bg-emerald-500 text-slate-950 font-extrabold shadow-md'"
                            class="px-3.5 py-1.5 rounded-xl font-bold text-xs transition-all active:scale-95">
                            <span x-text="selectedDistribusi && selectedDistribusi.signed ? '↩️ Batalkan TTD BSrE' : '✍️ TTD BSrE'"></span>
                        </button>

                        <button type="button" @click="printCurrent()" class="px-4 py-1.5 rounded-xl bg-purple-500 text-slate-950 font-bold text-xs shadow-lg">
                            🖨️ Cetak Surat
                        </button>
                        <button type="button" @click="showPrintDistribusiModal = false" class="p-1 rounded-lg text-slate-400 hover:text-white font-bold text-lg">&times;</button>
                    </div>
                </div>

                <!-- Formulir Edit Live BAST Distribusi -->
                <template x-if="selectedDistribusi">
                    <div x-show="showEditDistribusiForm" class="no-print bg-slate-950 p-4 rounded-2xl border border-teal-500/40 text-xs space-y-3 shadow-inner">
                        <div class="font-bold text-teal-300 text-[11px] uppercase tracking-wider border-b border-slate-800 pb-2">
                            ✏️ Live Edit Surat BAST Distribusi (Otomatis Berubah Pada Lembar Cetak):
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-2.5">
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Nomor Surat BAST</label>
                                <input type="text" x-model="selectedDistribusi.nomor_bast" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-teal-300 font-mono font-bold text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Hari</label>
                                <input type="text" x-model="selectedDistribusi.hari" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Tanggal Angka</label>
                                <input type="text" x-model="selectedDistribusi.tanggal_angka" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Bulan & Tahun</label>
                                <div class="flex space-x-1">
                                    <input type="text" x-model="selectedDistribusi.bulan" class="w-1/2 bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-white text-xs">
                                    <input type="text" x-model="selectedDistribusi.tahun" class="w-1/2 bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-white text-xs">
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-800">
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Pihak I (Pengurus Barang Menyerahkan)</label>
                                <input type="text" x-model="selectedDistribusi.pengurus_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white font-bold text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Pihak II (Sub Admin Penerima / Kepala Ruangan)</label>
                                <input type="text" x-model="selectedDistribusi.pj_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-emerald-300 font-bold text-xs">
                            </div>
                        </div>
                    </div>
                </template>

                <!-- LEMBAR CETAK BAST DISTRIBUSI -->
                <template x-if="selectedDistribusi">
                    <div class="bg-white text-black p-8 rounded-2xl font-serif shadow-2xl text-xs space-y-4 print:p-0 print:shadow-none">
                        
                        <div class="border-b-[3px] border-black pb-1 mb-0.5">
                            <div class="flex items-center justify-between gap-4">
                                <div class="w-20 shrink-0 flex justify-center">
                                    <img src="{{ asset('img/Logo-rsud/logo-rsud.png') }}" alt="Logo RSUD" class="w-16 h-16 object-contain">
                                </div>
                                <div class="flex-1 text-center font-sans text-black">
                                    <h4 class="font-bold text-xs sm:text-sm uppercase tracking-wide leading-tight">PEMERINTAH KABUPATEN BONDOWOSO</h4>
                                    <h3 class="font-black text-sm sm:text-base uppercase tracking-tight leading-tight">RUMAH SAKIT UMUM DAERAH dr. H. KOESNADI</h3>
                                    <p class="text-[10px] leading-tight mt-0.5">Jl. Kapten Pierre Tendean No. 3 Telepon (0332) 421974. Fax.0332 422311</p>
                                </div>
                                <div class="w-20 shrink-0 flex justify-center">
                                    <img src="{{ asset('img/Logo-rsud/logo-rsud.png') }}" alt="Logo RSUD" class="w-16 h-16 object-contain">
                                </div>
                            </div>
                        </div>
                        <div class="border-b border-black mb-4"></div>

                        <div class="text-center font-sans mb-3">
                            <h3 class="font-black text-xs sm:text-sm uppercase underline tracking-wider">BERITA ACARA SERAH TERIMA DISTRIBUSI ASET</h3>
                            <p class="text-[11px] font-semibold">Nomor : <span x-text="selectedDistribusi.nomor_bast"></span></p>
                        </div>

                        <p class="text-justify mb-2 leading-relaxed font-sans">
                            Pada hari ini <strong x-text="selectedDistribusi.hari"></strong> tanggal <strong x-text="selectedDistribusi.tanggal_angka"></strong> bulan <strong x-text="selectedDistribusi.bulan"></strong> tahun <strong x-text="selectedDistribusi.tahun"></strong>, telah dilaksanakan serah terima barang kepada unit penerima :
                        </p>

                        <div class="space-y-0.5 ml-4 font-sans text-[10.5px]">
                            <div class="flex"><div class="w-32 font-medium">Unit Penerima</div><div class="w-4">:</div><div class="flex-1 font-bold uppercase" x-text="selectedDistribusi.unit_nama"></div></div>
                            <div class="flex"><div class="w-32 font-medium">Kepala Ruangan / PJ</div><div class="w-4">:</div><div class="flex-1 font-bold" x-text="selectedDistribusi.pj_nama"></div></div>
                        </div>

                        <!-- TABEL RESMI BARANG DISTRIBUSI -->
                        <div class="my-3">
                            <table class="w-full text-center border-collapse border border-black text-[10px] font-sans">
                                <thead>
                                    <tr class="bg-gray-200 font-bold border-b border-black">
                                        <th class="border border-black px-2 py-1.5 w-8">No</th>
                                        <th class="border border-black px-3 py-1.5 text-left">Nama Barang / Aset</th>
                                        <th class="border border-black px-3 py-1.5 text-left">Merk & Spesifikasi</th>
                                        <th class="border border-black px-2 py-1.5 w-12">Vol</th>
                                        <th class="border border-black px-2 py-1.5 w-14">Satuan</th>
                                        <th class="border border-black px-2 py-1.5">Kondisi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(sub, idx) in selectedDistribusi.items" :key="idx">
                                        <tr class="border-b border-black">
                                            <td class="border border-black px-2 py-1.5" x-text="idx + 1"></td>
                                            <td class="border border-black px-3 py-1.5 text-left font-bold" x-text="sub.nama_barang"></td>
                                            <td class="border border-black px-3 py-1.5 text-left text-[9.5px]" x-text="sub.merk_type"></td>
                                            <td class="border border-black px-2 py-1.5 font-bold" x-text="sub.qty"></td>
                                            <td class="border border-black px-2 py-1.5" x-text="sub.satuan"></td>
                                            <td class="border border-black px-2 py-1.5 font-bold text-emerald-800" x-text="sub.kondisi"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <!-- TTD DUAL BSR-E -->
                        <div class="grid grid-cols-2 gap-8 text-center font-sans text-[10px] mt-6">
                            <div>
                                <p>Yang Menyerahkan (Pengurus Barang)</p>
                                <div class="h-16 flex items-center justify-center py-1">
                                    <div class="p-1 border border-teal-600 bg-teal-50 rounded flex items-center space-x-1.5">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=BSRE-PENGURUS-KOESNANDI" class="w-10 h-10">
                                        <div class="text-[7.5px] text-left leading-tight">
                                            <div class="font-bold">DITANDATANGANI ELEKTRONIK</div>
                                            <div>Pengurus Barang Aset RSUD</div>
                                        </div>
                                    </div>
                                </div>
                                <p class="font-bold underline uppercase" x-text="selectedDistribusi.pengurus_nama"></p>
                            </div>

                            <div>
                                <p>Yang Menerima (Sub-Admin Ruangan)</p>
                                <div class="h-16 flex items-center justify-center py-1">
                                    <template x-if="selectedDistribusi.signed">
                                        <div class="p-1 border border-emerald-600 bg-emerald-50 rounded flex items-center space-x-1.5">
                                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=BSRE-PENERIMA-KOESNANDI" class="w-10 h-10">
                                            <div class="text-[7.5px] text-left leading-tight">
                                                <div class="font-bold">DITANDATANGANI ELEKTRONIK</div>
                                                <div>Kepala Ruangan Penerima</div>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!selectedDistribusi.signed">
                                        <span class="text-amber-600 font-bold italic text-[10px]">( Belum Ditandatangani BSrE )</span>
                                    </template>
                                </div>
                                <p class="font-bold underline uppercase" x-text="selectedDistribusi.pj_nama"></p>
                            </div>
                        </div>

                    </div>
                </template>

            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL CETAK 3: LEMBAR DOKUMEN BAST MUTASI ASET (ANTAR RUANGAN)             -->
        <!-- ========================================================================= -->
        <div x-show="showPrintMutasiModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-2 sm:p-4 overflow-y-auto" x-cloak>
            <div @click.away="showPrintMutasiModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-4xl w-full p-4 sm:p-6 shadow-2xl space-y-4 my-auto relative">
                
                <div class="no-print flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
                    <div class="flex items-center space-x-2">
                        <span class="p-2 rounded-xl bg-rose-500/20 text-rose-300 text-sm">🔄</span>
                        <div>
                            <h3 class="text-base font-extrabold text-white">Berita Acara Serah Terima Mutasi Aset</h3>
                            <p class="text-[11px] text-slate-400" x-text="selectedMutasi ? ('Nomor BAST: ' + selectedMutasi.nomor_bast) : ''"></p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <button type="button" @click="showEditMutasiForm = !showEditMutasiForm"
                            class="px-3.5 py-1.5 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 font-bold text-xs transition-all">
                            <span x-text="showEditMutasiForm ? '✕ Tutup Form Edit' : '✏️ Edit Data & Pejabat BAST'"></span>
                        </button>

                        <!-- Toggle Button TTD / Batalkan TTD -->
                        <button type="button" @click="toggleSignMutasi(selectedMutasi)"
                            :class="selectedMutasi && selectedMutasi.signed ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40 hover:bg-rose-500/30' : 'bg-emerald-500 text-slate-950 font-extrabold shadow-md'"
                            class="px-3.5 py-1.5 rounded-xl font-bold text-xs transition-all active:scale-95">
                            <span x-text="selectedMutasi && selectedMutasi.signed ? '↩️ Batalkan TTD BSrE' : '✍️ TTD BSrE'"></span>
                        </button>

                        <button type="button" @click="printCurrent()" class="px-4 py-1.5 rounded-xl bg-purple-500 text-slate-950 font-bold text-xs shadow-lg">
                            🖨️ Cetak Surat
                        </button>
                        <button type="button" @click="showPrintMutasiModal = false" class="p-1 rounded-lg text-slate-400 hover:text-white font-bold text-lg">&times;</button>
                    </div>
                </div>

                <!-- Formulir Edit Live BAST Mutasi -->
                <template x-if="selectedMutasi">
                    <div x-show="showEditMutasiForm" class="no-print bg-slate-950 p-4 rounded-2xl border border-rose-500/40 text-xs space-y-3 shadow-inner">
                        <div class="font-bold text-rose-300 text-[11px] uppercase tracking-wider border-b border-slate-800 pb-2">
                            ✏️ Live Edit Surat BAST Mutasi (Otomatis Berubah Pada Lembar Cetak):
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-2.5">
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Nomor Surat BAST</label>
                                <input type="text" x-model="selectedMutasi.nomor_bast" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-rose-300 font-mono font-bold text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Hari</label>
                                <input type="text" x-model="selectedMutasi.hari" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Tanggal Angka</label>
                                <input type="text" x-model="selectedMutasi.tanggal_angka" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Bulan & Tahun</label>
                                <div class="flex space-x-1">
                                    <input type="text" x-model="selectedMutasi.bulan" class="w-1/2 bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-white text-xs">
                                    <input type="text" x-model="selectedMutasi.tahun" class="w-1/2 bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-white text-xs">
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-800">
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Ruangan Asal & PJ Menyerahkan</label>
                                <input type="text" x-model="selectedMutasi.asal" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white font-bold text-xs mb-1">
                                <input type="text" x-model="selectedMutasi.pj_asal_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-purple-300 font-bold text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Ruangan Tujuan & PJ Menerima</label>
                                <input type="text" x-model="selectedMutasi.tujuan" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-rose-300 font-bold text-xs mb-1">
                                <input type="text" x-model="selectedMutasi.pj_tujuan_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-emerald-300 font-bold text-xs">
                            </div>
                        </div>
                    </div>
                </template>

                <!-- LEMBAR CETAK BAST MUTASI -->
                <template x-if="selectedMutasi">
                    <div class="bg-white text-black p-8 rounded-2xl font-serif shadow-2xl text-xs space-y-4 print:p-0 print:shadow-none">
                        
                        <div class="border-b-[3px] border-black pb-1 mb-0.5">
                            <div class="flex items-center justify-between gap-4">
                                <div class="w-20 shrink-0 flex justify-center">
                                    <img src="{{ asset('img/Logo-rsud/logo-rsud.png') }}" alt="Logo RSUD" class="w-16 h-16 object-contain">
                                </div>
                                <div class="flex-1 text-center font-sans text-black">
                                    <h4 class="font-bold text-xs sm:text-sm uppercase tracking-wide leading-tight">PEMERINTAH KABUPATEN BONDOWOSO</h4>
                                    <h3 class="font-black text-sm sm:text-base uppercase tracking-tight leading-tight">RUMAH SAKIT UMUM DAERAH dr. H. KOESNADI</h3>
                                    <p class="text-[10px] leading-tight mt-0.5">Jl. Kapten Pierre Tendean No. 3 Telepon (0332) 421974. Fax.0332 422311</p>
                                </div>
                                <div class="w-20 shrink-0 flex justify-center">
                                    <img src="{{ asset('img/Logo-rsud/logo-rsud.png') }}" alt="Logo RSUD" class="w-16 h-16 object-contain">
                                </div>
                            </div>
                        </div>
                        <div class="border-b border-black mb-4"></div>

                        <div class="text-center font-sans mb-3">
                            <h3 class="font-black text-xs sm:text-sm uppercase underline tracking-wider">BERITA ACARA SERAH TERIMA MUTASI ASET</h3>
                            <p class="text-[11px] font-semibold">Nomor : <span x-text="selectedMutasi.nomor_bast"></span></p>
                        </div>

                        <p class="text-justify mb-2 leading-relaxed font-sans">
                            Pada hari ini <strong x-text="selectedMutasi.hari"></strong> tanggal <strong x-text="selectedMutasi.tanggal_angka"></strong> bulan <strong x-text="selectedMutasi.bulan"></strong> tahun <strong x-text="selectedMutasi.tahun"></strong>, telah dilaksanakan pemindahan/mutasi barang dari ruangan asal ke ruangan tujuan:
                        </p>

                        <div class="space-y-0.5 ml-4 font-sans text-[10.5px]">
                            <div class="flex"><div class="w-32 font-medium">Ruangan Asal</div><div class="w-4">:</div><div class="flex-1 font-bold" x-text="selectedMutasi.asal"></div></div>
                            <div class="flex"><div class="w-32 font-medium">Ruangan Tujuan</div><div class="w-4">:</div><div class="flex-1 font-bold text-rose-900" x-text="selectedMutasi.tujuan"></div></div>
                        </div>

                        <!-- TABEL RESMI MUTASI -->
                        <div class="my-3">
                            <table class="w-full text-center border-collapse border border-black text-[10px] font-sans">
                                <thead>
                                    <tr class="bg-gray-200 font-bold border-b border-black">
                                        <th class="border border-black px-2 py-1.5 w-8">No</th>
                                        <th class="border border-black px-3 py-1.5 text-left">Nama Barang Dimutasi</th>
                                        <th class="border border-black px-3 py-1.5 font-mono">Kode Rekening 108</th>
                                        <th class="border border-black px-2 py-1.5 w-12">Vol</th>
                                        <th class="border border-black px-2 py-1.5 w-14">Satuan</th>
                                        <th class="border border-black px-3 py-1.5 text-left">Alasan Pemindahan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b border-black">
                                        <td class="border border-black px-2 py-1.5">1</td>
                                        <td class="border border-black px-3 py-1.5 text-left font-bold" x-text="selectedMutasi.nama"></td>
                                        <td class="border border-black px-3 py-1.5 font-mono" x-text="selectedMutasi.kode_barang"></td>
                                        <td class="border border-black px-2 py-1.5 font-bold" x-text="selectedMutasi.qty"></td>
                                        <td class="border border-black px-2 py-1.5" x-text="selectedMutasi.satuan"></td>
                                        <td class="border border-black px-3 py-1.5 text-left text-[9.5px]" x-text="selectedMutasi.keterangan"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- TTD DUAL BSR-E MUTASI -->
                        <div class="grid grid-cols-2 gap-8 text-center font-sans text-[10px] mt-6">
                            <div>
                                <p>Yang Menyerahkan (Ruangan Asal)</p>
                                <div class="h-16 flex items-center justify-center py-1">
                                    <div class="p-1 border border-purple-600 bg-purple-50 rounded flex items-center space-x-1.5">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=BSRE-MUTASI-ASAL" class="w-10 h-10">
                                        <div class="text-[7.5px] text-left leading-tight">
                                            <div class="font-bold">DITANDATANGANI ELEKTRONIK</div>
                                            <div>Penanggung Jawab Ruangan Asal</div>
                                        </div>
                                    </div>
                                </div>
                                <p class="font-bold underline uppercase" x-text="selectedMutasi.pj_asal_nama"></p>
                            </div>

                            <div>
                                <p>Yang Menerima (Ruangan Tujuan)</p>
                                <div class="h-16 flex items-center justify-center py-1">
                                    <template x-if="selectedMutasi.signed">
                                        <div class="p-1 border border-rose-600 bg-rose-50 rounded flex items-center space-x-1.5">
                                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=BSRE-MUTASI-TUJUAN" class="w-10 h-10">
                                            <div class="text-[7.5px] text-left leading-tight">
                                                <div class="font-bold">DITANDATANGANI ELEKTRONIK</div>
                                                <div>Penanggung Jawab Ruangan Tujuan</div>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!selectedMutasi.signed">
                                        <span class="text-amber-600 font-bold italic text-[10px]">( Belum Ditandatangani BSrE )</span>
                                    </template>
                                </div>
                                <p class="font-bold underline uppercase" x-text="selectedMutasi.pj_tujuan_nama"></p>
                            </div>
                        </div>

                    </div>
                </template>

            </div>
        </div>

    </div>
</x-layout>
