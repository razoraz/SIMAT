<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Astap;
use App\Models\AstapRegister;
use App\Models\JenisAstap;
use App\Models\RekeningBelanja;
use App\Models\JenisPengadaan;
use App\Models\Unit;
use App\Models\User;

class AstapSeeder extends Seeder
{
    public function run(): void
    {
        // Temukan user admin default
        $user = User::first();
        $userId = $user ? $user->id : null;

        // Kosongkan tabel astaps dan astap_registers
        AstapRegister::query()->delete();
        Astap::query()->delete();

        // 1. KIB B: Laptop Operasional Asus ExpertBook B1 (5 Unit)
        $astapLaptop = Astap::create([
            'category' => 'KIB B',
            'kode_108' => '1.3.2.05.02.06.001',
            'nama_barang' => 'Laptop Operasional Asus ExpertBook B1',
            'tahun_perolehan' => 2026,
            'jumlah_volume' => 5,
            'satuan' => 'Unit',
            'harga_satuan' => 15000000,
            'total_realisasi' => 75000000,
            'biaya_administrasi_proyek' => 1000000,
            'is_extracomtable' => false,
            'spk_nomor' => '028/SPK-KTR/V/2026',
            'spk_tanggal' => '2026-05-12',
            'surat_pesanan_nomor' => '028/SP-RSUD/V/2026',
            'surat_pesanan_tanggal' => '2026-05-15',
            'kwitansi_nomor' => 'KW-028/KTR/2026',
            'kwitansi_tanggal' => '2026-06-02',
            'faktur_nomor' => 'INV-2026-028',
            'faktur_tanggal' => '2026-06-05',
            'sp2d_nomor' => '0129/SP2D/BLUD/2026',
            'sp2d_tanggal' => '2026-06-15',
            'bast_dokumen_nomor' => '000.2.3.2/224/430.10.7/2026',
            'bast_dokumen_tanggal' => '2026-06-30',
            'alamat_barang' => 'Kompleks RSUD Dr. H. Koesnandi Bondowoso',
            'penyedia_nama' => 'CV Multi Media Solusindo',
            'penyedia_pemilik' => 'Ir. H. Budi Santoso',
            'ppk_nama' => 'dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR.',
            'ppk_nip' => '19771002 200604 1 006',
            'keterangan_tambahan' => 'Pengadaan laptop operasional tim medis & IT RME SatuSehat',
            'spesifikasi_json' => [
                'merk' => 'Asus',
                'type' => 'ExpertBook B1400',
                'ukuran' => '14 Inch FHD / Core i7-1355U / 16GB RAM / 512GB SSD',
                'bahan' => 'Aluminium & Magnesium Alloy',
                'no_pabrik' => 'SN-ASUS-2026-LPT'
            ],
            'user_id' => $userId
        ]);

        // Buat 5 Unit Registers NIBAR (Sebagian ditempatkan, sebagian di gudang)
        $ruanganSample = [
            'Ruang Direksi & Sekretariat RSUD',
            'Ruang Rekam Medis (RME & SatuSehat)',
            'Instalasi Rawat Inap Paviliun Melati',
            null, // Belum ditempatkan / Di gudang
            null  // Belum ditempatkan / Di gudang
        ];

        for ($i = 1; $i <= 5; $i++) {
            $noRegStr = str_pad($i, 7, '0', STR_PAD_LEFT);
            $nibar = "12013511.0200000028.00002026.132050206001.{$noRegStr}";
            
            AstapRegister::create([
                'astap_id' => $astapLaptop->id,
                'kode_108' => '1.3.2.05.02.06.001',
                'tahun_perolehan' => 2026,
                'no_register_int' => $i,
                'no_register' => $noRegStr,
                'nibar' => $nibar,
                'ruang_pemegang' => $ruanganSample[$i - 1],
                'kondisi' => ($i == 3) ? 'Rusak Ringan' : 'Baik',
                'status_mutasi' => 'Tersedia'
            ]);
        }

        // 2. KIB B: Submersible Pump 7.5 HP (1 Unit)
        $astapPompa = Astap::create([
            'category' => 'KIB B',
            'kode_108' => '1.3.2.01.03.05.005',
            'nama_barang' => 'Submersible Pump 7.5 HP Sentral',
            'tahun_perolehan' => 2025,
            'jumlah_volume' => 1,
            'satuan' => 'Unit',
            'harga_satuan' => 41501900,
            'total_realisasi' => 42501900,
            'biaya_administrasi_proyek' => 1000000,
            'is_extracomtable' => false,
            'spk_nomor' => '019/SPK-PMP/VI/2025',
            'spk_tanggal' => '2025-06-01',
            'surat_pesanan_nomor' => '019/BN.BA/VI/2025',
            'kwitansi_nomor' => 'KW-019/PMP/2025',
            'faktur_nomor' => 'INV-2025-091',
            'sp2d_nomor' => '019/SP2D/BLUD/2025',
            'bast_dokumen_nomor' => '000.2.3.2/019/2025',
            'alamat_barang' => 'Area Tandon Sentral Belakang RSUD',
            'penyedia_nama' => 'CV Mitra Teknik Mandiri',
            'ppk_nama' => 'dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR.',
            'ppk_nip' => '19771002 200604 1 006',
            'keterangan_tambahan' => 'Operasional normal pompa cadangan air bersih rumah sakit',
            'spesifikasi_json' => [
                'merk' => 'Franklin Electric',
                'type' => '2347288602G',
                'ukuran' => '7.5HP 3-Phase Max Head 139m',
                'bahan' => 'Campuran Baja Medis',
                'no_pabrik' => '23K14-17-0006'
            ],
            'user_id' => $userId
        ]);

        AstapRegister::create([
            'astap_id' => $astapPompa->id,
            'kode_108' => '1.3.2.01.03.05.005',
            'tahun_perolehan' => 2025,
            'no_register_int' => 1,
            'no_register' => '0000001',
            'nibar' => '12013511.0200000028.00002025.132010305005.0000001',
            'ruang_pemegang' => 'Instalasi Sanitasi & IPSRS RSUD',
            'kondisi' => 'Baik',
            'status_mutasi' => 'Tersedia'
        ]);

        // 3. KIB B: CT-Scan 128 Slice High Resolution (1 Unit)
        $astapCtscan = Astap::create([
            'category' => 'KIB B',
            'kode_108' => '1.3.2.02.01.01.005',
            'nama_barang' => 'CT-Scan 128 Slice High Resolution',
            'tahun_perolehan' => 2024,
            'jumlah_volume' => 1,
            'satuan' => 'Unit',
            'harga_satuan' => 1445000000,
            'total_realisasi' => 1450000000,
            'biaya_administrasi_proyek' => 5000000,
            'is_extracomtable' => false,
            'spk_nomor' => '045/SPK-RAD/VII/2024',
            'spk_tanggal' => '2024-07-10',
            'kwitansi_nomor' => 'KW-045/RAD/2024',
            'faktur_nomor' => 'INV-RAD-2024-01',
            'sp2d_nomor' => '045/SP2D/DAK/2024',
            'bast_dokumen_nomor' => '000.2.3.2/045/2024',
            'alamat_barang' => 'Gedung Pusat Diagnostik Terpadu Lt 1',
            'penyedia_nama' => 'PT Siemens Healthineers Indonesia',
            'ppk_nama' => 'dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR.',
            'ppk_nip' => '19771002 200604 1 006',
            'keterangan_tambahan' => 'Terkalibrasi BAPETEN dan operasional 24 jam',
            'spesifikasi_json' => [
                'merk' => 'Siemens SOMATOM',
                'type' => '128-Slice Perspective',
                'ukuran' => 'Dual Energy Stellar Detector',
                'bahan' => 'Logam & Komponen Radiologi Medis',
                'no_pabrik' => 'SN-99812-RAD'
            ],
            'user_id' => $userId
        ]);

        AstapRegister::create([
            'astap_id' => $astapCtscan->id,
            'kode_108' => '1.3.2.02.01.01.005',
            'tahun_perolehan' => 2024,
            'no_register_int' => 1,
            'no_register' => '0000001',
            'nibar' => '12013511.0200000028.00002024.132020101005.0000001',
            'ruang_pemegang' => 'Instalasi Radiologi & Imaging',
            'kondisi' => 'Baik',
            'status_mutasi' => 'Tersedia'
        ]);

        // 4. KIB A: Lahan Bangunan RSUD Dr. H. Koesnandi (1 Bidang)
        $astapTanah = Astap::create([
            'category' => 'KIB A',
            'kode_108' => '1.3.1.01.01.02.013',
            'nama_barang' => 'Lahan Bangunan RSUD Dr. H. Koesnandi',
            'tahun_perolehan' => 1984,
            'jumlah_volume' => 1,
            'satuan' => 'Bidang',
            'harga_satuan' => 8500000000,
            'total_realisasi' => 8500000000,
            'biaya_administrasi_proyek' => 0,
            'is_extracomtable' => false,
            'spk_nomor' => 'SK-BPN/1984/01',
            'spk_tanggal' => '1984-03-12',
            'sp2d_nomor' => '042/SP2D/1984',
            'bast_dokumen_nomor' => '000.2.3.2/042/1984',
            'alamat_barang' => 'Jl. Piere Tendean No. 1, Kel. Badean, Bondowoso',
            'penyedia_nama' => 'Pemerintah Kabupaten Bondowoso',
            'ppk_nama' => 'dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR.',
            'ppk_nip' => '19771002 200604 1 006',
            'keterangan_tambahan' => 'Batas lahan terpagar penuh dan sertifikat hak pakai aktif',
            'spesifikasi_json' => [
                'luas_m2' => 35400,
                'hak_tanah' => 'Hak Pakai',
                'sertifikat_no' => 'HP-108/1984',
                'sertifikat_tgl' => '1984-03-15',
                'penggunaan' => 'Bangunan Rumah Sakit & Fasilitas Kesehatan'
            ],
            'user_id' => $userId
        ]);

        AstapRegister::create([
            'astap_id' => $astapTanah->id,
            'kode_108' => '1.3.1.01.01.02.013',
            'tahun_perolehan' => 1984,
            'no_register_int' => 1,
            'no_register' => '0000001',
            'nibar' => '12013511.0200000028.00001984.131010102013.0000001',
            'ruang_pemegang' => 'Kompleks Utama RSUD Dr. H. Koesnandi',
            'kondisi' => 'Baik',
            'status_mutasi' => 'Tersedia'
        ]);

        // 5. KIB C: Gedung Paviliun Graha Amukti VIP (1 Gedung)
        $astapGedung = Astap::create([
            'category' => 'KIB C',
            'kode_108' => '1.3.3.01.01.01.008',
            'nama_barang' => 'Gedung Paviliun Graha Amukti VIP',
            'tahun_perolehan' => 2018,
            'jumlah_volume' => 1,
            'satuan' => 'Gedung',
            'harga_satuan' => 4200000000,
            'total_realisasi' => 4200000000,
            'biaya_administrasi_proyek' => 40000000,
            'is_extracomtable' => false,
            'spk_nomor' => 'SPK-GRH/2018/01',
            'spk_tanggal' => '2018-02-10',
            'sp2d_nomor' => '078/SP2D/2018',
            'bast_dokumen_nomor' => '000.2.3.2/078/2018',
            'alamat_barang' => 'Kompleks Barat RSUD Dr. H. Koesnandi',
            'penyedia_nama' => 'PT Karya Bangun Persada',
            'ppk_nama' => 'dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR.',
            'ppk_nip' => '19771002 200604 1 006',
            'keterangan_tambahan' => 'Kapasitas 24 kamar VIP & VVIP dengan fasilitas terintegrasi',
            'spesifikasi_json' => [
                'luas_m2' => 2800,
                'bertingkat' => 'Bertingkat (2 Lt)',
                'beton' => 'Beton Bertulang',
                'status_tanah' => 'Tanah Hak Pakai RSUD',
                'kode_aset_tanah' => '1.3.1.01.01.02.013'
            ],
            'user_id' => $userId
        ]);

        AstapRegister::create([
            'astap_id' => $astapGedung->id,
            'kode_108' => '1.3.3.01.01.01.008',
            'tahun_perolehan' => 2018,
            'no_register_int' => 1,
            'no_register' => '0000001',
            'nibar' => '12013511.0200000028.00002018.133010101008.0000001',
            'ruang_pemegang' => 'Paviliun Graha Amukti VIP',
            'kondisi' => 'Baik',
            'status_mutasi' => 'Tersedia'
        ]);

        // 6. KIB D: Jaringan Pipa Oksigen Sentral Medis (1 Paket)
        $astapJaringan = Astap::create([
            'category' => 'KIB D',
            'kode_108' => '1.3.4.03.01.01.004',
            'nama_barang' => 'Jaringan Pipa Oksigen Sentral Medis',
            'tahun_perolehan' => 2020,
            'jumlah_volume' => 1,
            'satuan' => 'Paket',
            'harga_satuan' => 650000000,
            'total_realisasi' => 650000000,
            'biaya_administrasi_proyek' => 15000000,
            'is_extracomtable' => false,
            'spk_nomor' => 'SPK-OKS/2020/08',
            'spk_tanggal' => '2020-05-14',
            'sp2d_nomor' => '088/SP2D/2020',
            'bast_dokumen_nomor' => '000.2.3.2/088/2020',
            'alamat_barang' => 'Seluruh Paviliun Rawat Inap & IGD RSUD',
            'penyedia_nama' => 'CV Sumber Sehat Teknik',
            'ppk_nama' => 'dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR.',
            'ppk_nip' => '19771002 200604 1 006',
            'keterangan_tambahan' => 'Pipa gas medis tembaga terpasang ke 120 bed pasien',
            'spesifikasi_json' => [
                'konstruksi' => 'Copper Pipe Medical Grade Sentral ASTM B819',
                'panjang_m' => 1200,
                'lebar_m' => 0.05,
                'status_tanah' => 'Tanah Hak Pakai RSUD'
            ],
            'user_id' => $userId
        ]);

        AstapRegister::create([
            'astap_id' => $astapJaringan->id,
            'kode_108' => '1.3.4.03.01.01.004',
            'tahun_perolehan' => 2020,
            'no_register_int' => 1,
            'no_register' => '0000001',
            'nibar' => '12013511.0200000028.00002020.134030101004.0000001',
            'ruang_pemegang' => 'Instalasi Gas Medis & IPSRS',
            'kondisi' => 'Rusak Ringan',
            'status_mutasi' => 'Tersedia'
        ]);

        // 7. KIB E: Buku Jurnal Kedokteran & Farmakologi (50 Eksemplar)
        $astapBuku = Astap::create([
            'category' => 'KIB E',
            'kode_108' => '1.3.5.01.01.01.002',
            'nama_barang' => 'Buku Jurnal Kedokteran & Farmakologi',
            'tahun_perolehan' => 2021,
            'jumlah_volume' => 50,
            'satuan' => 'Eksemplar',
            'harga_satuan' => 1700000,
            'total_realisasi' => 85000000,
            'biaya_administrasi_proyek' => 2000000,
            'is_extracomtable' => false,
            'spk_nomor' => '012/SPK-BKO/2021',
            'spk_tanggal' => '2021-08-01',
            'sp2d_nomor' => '055/SP2D/2021',
            'bast_dokumen_nomor' => '000.2.3.2/055/2021',
            'alamat_barang' => 'Gedung Diklit & Perpustakaan Medis RSUD',
            'penyedia_nama' => 'PT Elsevier Indonesia',
            'ppk_nama' => 'dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR.',
            'ppk_nip' => '19771002 200604 1 006',
            'keterangan_tambahan' => 'Tersedia untuk referensi dokter spesialis & residen',
            'spesifikasi_json' => [
                'buku_judul' => 'Pedoman Standar Pelayanan Klinis & Formularium RSUD',
                'buku_pencipta' => 'Komite Medik & Tim Farmasi Klinis',
                'buku_spesifikasi' => 'Hardcover Vol 1-12 Edisi Internasional'
            ],
            'user_id' => $userId
        ]);

        for ($i = 1; $i <= 5; $i++) {
            $noRegStr = str_pad($i, 7, '0', STR_PAD_LEFT);
            AstapRegister::create([
                'astap_id' => $astapBuku->id,
                'kode_108' => '1.3.5.01.01.01.002',
                'tahun_perolehan' => 2021,
                'no_register_int' => $i,
                'no_register' => $noRegStr,
                'nibar' => "12013511.0200000028.00002021.135010101002.{$noRegStr}",
                'ruang_pemegang' => 'Instalasi Perpustakaan Medis',
                'kondisi' => 'Baik',
                'status_mutasi' => 'Tersedia'
            ]);
        }

        // 8. KIB F: Pembangunan Gedung Rawat Inap Terpadu Lt 3 (KDP - 1 Gedung)
        $astapKdp = Astap::create([
            'category' => 'KIB F',
            'kode_108' => '1.3.6.01.01.01.001',
            'nama_barang' => 'Pembangunan Gedung Rawat Inap Terpadu Lt 3',
            'tahun_perolehan' => 2026,
            'jumlah_volume' => 1,
            'satuan' => 'Gedung',
            'harga_satuan' => 3500000000,
            'total_realisasi' => 3500000000,
            'biaya_administrasi_proyek' => 40000000,
            'is_extracomtable' => false,
            'spk_nomor' => '015/SPK-KDP/2026',
            'spk_tanggal' => '2026-01-10',
            'sp2d_nomor' => '015/SP2D/2026',
            'bast_dokumen_nomor' => '000.2.3.2/015/MC-03/2026',
            'alamat_barang' => 'Kompleks Belakang Paviliun Melati RSUD',
            'penyedia_nama' => 'PT Pembangunan Mandiri',
            'ppk_nama' => 'dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR.',
            'ppk_nip' => '19771002 200604 1 006',
            'keterangan_tambahan' => 'Progres fisik konstruksi 60%, target selesai akhir 2026',
            'spesifikasi_json' => [
                'luas_m2' => 3200,
                'progres_persen' => 60,
                'bangunan' => 'Bertingkat (3 Lt)',
                'beton' => 'Beton Bertulang K-350',
                'status_tanah' => 'Tanah Hak Pakai RSUD'
            ],
            'user_id' => $userId
        ]);

        AstapRegister::create([
            'astap_id' => $astapKdp->id,
            'kode_108' => '1.3.6.01.01.01.001',
            'tahun_perolehan' => 2026,
            'no_register_int' => 1,
            'no_register' => '0000001',
            'nibar' => '12013511.0200000028.00002026.136010101001.0000001',
            'ruang_pemegang' => 'Area Proyek KDP Belakang Paviliun Melati',
            'kondisi' => 'Dalam Renovasi',
            'status_mutasi' => 'Tersedia'
        ]);

        // 9. ATB: Software SIMAT-RK RSUD & EMR Cloud (1 Lisensi)
        $astapAtb = Astap::create([
            'category' => 'ATB',
            'kode_108' => '1.5.3.01.01.01.005',
            'nama_barang' => 'Software SIMRS Terintegrasi & EMR Cloud',
            'tahun_perolehan' => 2024,
            'jumlah_volume' => 1,
            'satuan' => 'Lisensi',
            'harga_satuan' => 445000000,
            'total_realisasi' => 450000000,
            'biaya_administrasi_proyek' => 5000000,
            'is_extracomtable' => false,
            'spk_nomor' => '077/SPK-SIMRS/2024',
            'spk_tanggal' => '2024-03-20',
            'sp2d_nomor' => '077/SP2D/2024',
            'bast_dokumen_nomor' => '000.2.3.2/077/2024',
            'alamat_barang' => 'Server Room Sentral IT RSUD',
            'penyedia_nama' => 'PT Medika Solusindo Digital',
            'ppk_nama' => 'dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR.',
            'ppk_nip' => '19771002 200604 1 006',
            'keterangan_tambahan' => 'Lisensi permanen terintegrasi RME SatuSehat & BPJS VClaim',
            'spesifikasi_json' => [
                'judul_lisensi' => 'SIMAT Health Enterprise Server V4.2',
                'pencipta' => 'PT Medika Solusindo Digital',
                'spesifikasi' => 'Enterprise Server Multi-Unit Unlimited Client'
            ],
            'user_id' => $userId
        ]);

        AstapRegister::create([
            'astap_id' => $astapAtb->id,
            'kode_108' => '1.5.3.01.01.01.005',
            'tahun_perolehan' => 2024,
            'no_register_int' => 1,
            'no_register' => '0000001',
            'nibar' => '12013511.0200000028.00002024.153010101005.0000001',
            'ruang_pemegang' => 'Instalasi IT & SIMRS',
            'kondisi' => 'Baik',
            'status_mutasi' => 'Tersedia'
        ]);

        // 10. EXTRACOM: Gunting Angkat Jahitan Littauer 14cm (10 Pcs < Rp 300rb)
        $astapGunting = Astap::create([
            'category' => 'EXTRACOM',
            'kode_108' => '1.3.2.02.01.01.099',
            'nama_barang' => 'Gunting Angkat Jahitan Littauer 14cm',
            'tahun_perolehan' => 2026,
            'jumlah_volume' => 10,
            'satuan' => 'Pcs',
            'harga_satuan' => 85000,
            'total_realisasi' => 850000,
            'biaya_administrasi_proyek' => 0,
            'is_extracomtable' => true,
            'spk_nomor' => '011/SPK-EXT/2026',
            'spk_tanggal' => '2026-02-05',
            'sp2d_nomor' => '011/SP2D/2026',
            'bast_dokumen_nomor' => '000.2.3.2/011/2026',
            'alamat_barang' => 'Depo Farmasi & Bedah Sentral',
            'penyedia_nama' => 'CV Medika Alat Kesehatan',
            'ppk_nama' => 'dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR.',
            'ppk_nip' => '19771002 200604 1 006',
            'keterangan_tambahan' => 'Nilai per unit Rp 85.000 (< Rp 300.000 masuk Ekstrakomtabel)',
            'spesifikasi_json' => [
                'merk' => 'Surgical Instrument',
                'type' => 'Littauer 14cm',
                'bahan' => 'Stainless Steel Medis',
                'no_pabrik' => 'LT-14-001'
            ],
            'user_id' => $userId
        ]);

        for ($i = 1; $i <= 3; $i++) {
            $noRegStr = str_pad($i, 7, '0', STR_PAD_LEFT);
            AstapRegister::create([
                'astap_id' => $astapGunting->id,
                'kode_108' => '1.3.2.02.01.01.099',
                'tahun_perolehan' => 2026,
                'no_register_int' => $i,
                'no_register' => $noRegStr,
                'nibar' => "12013511.0200000028.00002026.132020101099.{$noRegStr}",
                'ruang_pemegang' => ($i == 1) ? 'IGD & Poliklinik Bedah' : null,
                'kondisi' => 'Baik',
                'status_mutasi' => 'Tersedia'
            ]);
        }

        // 11. EXTRACOM: Timbangan Bayi Analog (5 Unit < Rp 300rb)
        $astapTimbangan = Astap::create([
            'category' => 'EXTRACOM',
            'kode_108' => '1.3.2.02.01.02.045',
            'nama_barang' => 'Timbangan Bayi Analog Akurat',
            'tahun_perolehan' => 2026,
            'jumlah_volume' => 5,
            'satuan' => 'Unit',
            'harga_satuan' => 245000,
            'total_realisasi' => 1225000,
            'biaya_administrasi_proyek' => 0,
            'is_extracomtable' => true,
            'spk_nomor' => '012/SPK-EXT/2026',
            'spk_tanggal' => '2026-02-15',
            'sp2d_nomor' => '012/SP2D/2026',
            'bast_dokumen_nomor' => '000.2.3.2/012/2026',
            'alamat_barang' => 'Kamar Bersalin & Poli Anak',
            'penyedia_nama' => 'CV Tri Bintang Medika',
            'ppk_nama' => 'dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR.',
            'ppk_nip' => '19771002 200604 1 006',
            'keterangan_tambahan' => 'Nilai per unit Rp 245.000 (< Rp 300.000 masuk Ekstrakomtabel)',
            'spesifikasi_json' => [
                'merk' => 'Crown Baby',
                'type' => 'CR-20 Analog',
                'bahan' => 'Plastik ABS & Pegas Baja',
                'no_pabrik' => 'CRW-2026-01'
            ],
            'user_id' => $userId
        ]);

        for ($i = 1; $i <= 2; $i++) {
            $noRegStr = str_pad($i, 7, '0', STR_PAD_LEFT);
            AstapRegister::create([
                'astap_id' => $astapTimbangan->id,
                'kode_108' => '1.3.2.02.01.02.045',
                'tahun_perolehan' => 2026,
                'no_register_int' => $i,
                'no_register' => $noRegStr,
                'nibar' => "12013511.0200000028.00002026.132020102045.{$noRegStr}",
                'ruang_pemegang' => 'Paviliun Anak & Perinatologi',
                'kondisi' => 'Baik',
                'status_mutasi' => 'Tersedia'
            ]);
        }
    }
}
