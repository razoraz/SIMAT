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

        // Helper Resolvers Relasi Foreign Key
        $getJenisAstapId = function(string $kode108) {
            return JenisAstap::where('sub_sub_rincian_objek', $kode108)->value('id') 
                ?? JenisAstap::where('jenis', substr($kode108, 0, 5))->value('id')
                ?? JenisAstap::first()?->id;
        };

        $getRekeningId = function(string $prefixRek) {
            return RekeningBelanja::where('kode_rek', 'LIKE', $prefixRek . '%')->value('id') 
                ?? RekeningBelanja::first()?->id;
        };

        $getJenisPengadaanId = function(string $subKegKode) {
            return JenisPengadaan::where('sub_kegiatan_kode', 'LIKE', '%' . $subKegKode . '%')->value('id') 
                ?? JenisPengadaan::first()?->id;
        };

        $resolveUnitId = function (?string $ruangName) {
            if (empty($ruangName)) {
                return null;
            }
            if (str_contains($ruangName, 'Direksi')) {
                return Unit::where('nama', 'LIKE', '%Direktur%')->value('id');
            }
            if (str_contains($ruangName, 'Rekam Medis') || str_contains($ruangName, 'Rekam Medik')) {
                return Unit::where('nama', 'LIKE', '%Rekam Medik%')->value('id');
            }
            if (str_contains($ruangName, 'Melati')) {
                return Unit::where('nama', 'LIKE', '%Melati%')->value('id');
            }
            if (str_contains($ruangName, 'Sanitasi') || str_contains($ruangName, 'IPSRS')) {
                return Unit::where('nama', 'LIKE', '%IPS RS%')->value('id') ?? Unit::where('nama', 'LIKE', '%Sanitasi%')->value('id');
            }
            if (str_contains($ruangName, 'Radiologi')) {
                return Unit::where('nama', 'LIKE', '%Radiologi%')->value('id');
            }
            if (str_contains($ruangName, 'Graha Amukti') || str_contains($ruangName, 'Paviliun')) {
                return Unit::where('nama', 'LIKE', '%Bougenville%')->value('id') ?? Unit::where('tipe', 'Rawat Inap & Paviliun')->value('id');
            }
            if (str_contains($ruangName, 'Gas Medis')) {
                return Unit::where('nama', 'LIKE', '%IPS RS%')->value('id');
            }
            if (str_contains($ruangName, 'Perpustakaan')) {
                return Unit::where('nama', 'LIKE', '%Informasi%')->value('id') ?? Unit::where('tipe', 'Manajemen & Struktural')->value('id');
            }
            if (str_contains($ruangName, 'IT')) {
                return Unit::where('nama', 'LIKE', '%Informasi Teknologi%')->value('id');
            }
            if (str_contains($ruangName, 'IGD') || str_contains($ruangName, 'Bedah')) {
                return Unit::where('nama', 'LIKE', '%Bedah Sentral%')->value('id') ?? Unit::where('nama', 'LIKE', '%IGD%')->value('id');
            }
            if (str_contains($ruangName, 'Anak') || str_contains($ruangName, 'Perinatologi')) {
                return Unit::where('nama', 'LIKE', '%Dahlia%')->value('id');
            }
            return Unit::first()?->id;
        };

        // Kosongkan tabel astaps dan astap_registers
        AstapRegister::query()->delete();
        Astap::query()->delete();

        // 1. KIB B: Laptop Operasional Asus ExpertBook B1 (5 Unit)
        $astapLaptop = Astap::create([
            'jenis_pengadaan_id' => $getJenisPengadaanId('0002'),
            'rekening_belanja_id' => $getRekeningId('5.2.02.08'),
            'jenis_astap_id' => $getJenisAstapId('1.3.2.05.02.06.001'),
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
            $nibar = "12013511020000002800002026132050206001{$noRegStr}";
            $ruang = $ruanganSample[$i - 1];
            
            AstapRegister::create([
                'astap_id' => $astapLaptop->id,
                'unit_id' => $resolveUnitId($ruang),
                'tahun_perolehan' => 2026,
                'no_register_int' => $i,
                'no_register' => $nibar,
                'nibar' => $nibar,
                'qr_code_path' => "/scan/{$nibar}",
                'ruang_pemegang' => $ruang,
                'kondisi' => ($i == 3) ? 'Rusak Ringan' : 'Baik',
                'status' => 'Tersedia'
            ]);
        }

        // 2. KIB B: Submersible Pump 7.5 HP (1 Unit)
        $astapPompa = Astap::create([
            'jenis_pengadaan_id' => $getJenisPengadaanId('0001'),
            'rekening_belanja_id' => $getRekeningId('5.2.02.01'),
            'jenis_astap_id' => $getJenisAstapId('1.3.2.01.03.05.005'),
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

        $nibarPompa = '120135110200000028000020251320103050050000001';
        $ruangPompa = 'Instalasi Sanitasi & IPSRS RSUD';
        AstapRegister::create([
            'astap_id' => $astapPompa->id,
            'unit_id' => $resolveUnitId($ruangPompa),
            'tahun_perolehan' => 2025,
            'no_register_int' => 1,
            'no_register' => $nibarPompa,
            'nibar' => $nibarPompa,
            'qr_code_path' => "/scan/{$nibarPompa}",
            'ruang_pemegang' => $ruangPompa,
            'kondisi' => 'Baik',
            'status' => 'Tersedia'
        ]);

        // 3. KIB B: CT-Scan 128 Slice High Resolution (1 Unit)
        $astapCtscan = Astap::create([
            'jenis_pengadaan_id' => $getJenisPengadaanId('0012'),
            'rekening_belanja_id' => $getRekeningId('5.2.02.02'),
            'jenis_astap_id' => $getJenisAstapId('1.3.2.02.01.01.005'),
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

        $nibarCt = '120135110200000028000020241320201010050000001';
        $ruangCt = 'Instalasi Radiologi & Imaging Sentral';
        AstapRegister::create([
            'astap_id' => $astapCtscan->id,
            'unit_id' => $resolveUnitId($ruangCt),
            'tahun_perolehan' => 2024,
            'no_register_int' => 1,
            'no_register' => $nibarCt,
            'nibar' => $nibarCt,
            'qr_code_path' => "/scan/{$nibarCt}",
            'ruang_pemegang' => $ruangCt,
            'kondisi' => 'Baik',
            'status' => 'Tersedia'
        ]);

        // 4. KIB A: Lahan Bangunan RSUD Dr. H. Koesnandi (1 Bidang)
        $astapTanah = Astap::create([
            'jenis_pengadaan_id' => $getJenisPengadaanId('0001'),
            'rekening_belanja_id' => $getRekeningId('5.2.01.01'),
            'jenis_astap_id' => $getJenisAstapId('1.3.1.01.01.02.013'),
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

        $nibarTanah = '120135110200000028000019841310101020130000001';
        $ruangTanah = 'Kompleks Utama RSUD Dr. H. Koesnandi';
        AstapRegister::create([
            'astap_id' => $astapTanah->id,
            'unit_id' => $resolveUnitId($ruangTanah),
            'tahun_perolehan' => 1984,
            'no_register_int' => 1,
            'no_register' => $nibarTanah,
            'nibar' => $nibarTanah,
            'qr_code_path' => "/scan/{$nibarTanah}",
            'ruang_pemegang' => $ruangTanah,
            'kondisi' => 'Baik',
            'status' => 'Tersedia'
        ]);

        // 5. KIB C: Gedung Paviliun Graha Amukti VIP (1 Gedung)
        $astapGedung = Astap::create([
            'jenis_pengadaan_id' => $getJenisPengadaanId('0005'),
            'rekening_belanja_id' => $getRekeningId('5.2.03.01'),
            'jenis_astap_id' => $getJenisAstapId('1.3.3.01.01.01.008'),
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

        $nibarGedung = '120135110200000028000020181330101010080000001';
        $ruangGedung = 'Paviliun Graha Amukti VIP';
        AstapRegister::create([
            'astap_id' => $astapGedung->id,
            'unit_id' => $resolveUnitId($ruangGedung),
            'tahun_perolehan' => 2018,
            'no_register_int' => 1,
            'no_register' => $nibarGedung,
            'nibar' => $nibarGedung,
            'qr_code_path' => "/scan/{$nibarGedung}",
            'ruang_pemegang' => $ruangGedung,
            'kondisi' => 'Baik',
            'status' => 'Tersedia'
        ]);

        // 6. KIB D: Jaringan Pipa Oksigen Sentral Medis (1 Paket)
        $astapJaringan = Astap::create([
            'jenis_pengadaan_id' => $getJenisPengadaanId('0001'),
            'rekening_belanja_id' => $getRekeningId('5.2.04.03'),
            'jenis_astap_id' => $getJenisAstapId('1.3.4.03.01.01.004'),
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

        $nibarJaringan = '120135110200000028000020201340301010040000001';
        $ruangJaringan = 'Instalasi Gas Medis & IPSRS';
        AstapRegister::create([
            'astap_id' => $astapJaringan->id,
            'unit_id' => $resolveUnitId($ruangJaringan),
            'tahun_perolehan' => 2020,
            'no_register_int' => 1,
            'no_register' => $nibarJaringan,
            'nibar' => $nibarJaringan,
            'qr_code_path' => "/scan/{$nibarJaringan}",
            'ruang_pemegang' => $ruangJaringan,
            'kondisi' => 'Rusak Ringan',
            'status' => 'Tersedia'
        ]);

        // 7. KIB E: Buku Jurnal Kedokteran & Farmakologi (50 Eksemplar)
        $astapBuku = Astap::create([
            'jenis_pengadaan_id' => $getJenisPengadaanId('0001'),
            'rekening_belanja_id' => $getRekeningId('5.2.05.01'),
            'jenis_astap_id' => $getJenisAstapId('1.3.5.01.01.01.002'),
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

        $ruangBuku = 'Instalasi Perpustakaan Medis';
        for ($i = 1; $i <= 5; $i++) {
            $noRegStr = str_pad($i, 7, '0', STR_PAD_LEFT);
            $nibar = "12013511020000002800002021135010101002{$noRegStr}";
            AstapRegister::create([
                'astap_id' => $astapBuku->id,
                'unit_id' => $resolveUnitId($ruangBuku),
                'tahun_perolehan' => 2021,
                'no_register_int' => $i,
                'no_register' => $nibar,
                'nibar' => $nibar,
                'qr_code_path' => "/scan/{$nibar}",
                'ruang_pemegang' => $ruangBuku,
                'kondisi' => 'Baik',
                'status' => 'Tersedia'
            ]);
        }

        // 8. KIB F: Pembangunan Gedung Rawat Inap Terpadu Lt 3 (KDP - 1 Gedung)
        $astapKdp = Astap::create([
            'jenis_pengadaan_id' => $getJenisPengadaanId('0005'),
            'rekening_belanja_id' => $getRekeningId('5.2.07.01'),
            'jenis_astap_id' => $getJenisAstapId('1.3.6.01.01.01.001'),
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

        $nibarKdp = '120135110200000028000020261360101010010000001';
        $ruangKdp = 'Area Proyek KDP Belakang Paviliun Melati';
        AstapRegister::create([
            'astap_id' => $astapKdp->id,
            'unit_id' => $resolveUnitId($ruangKdp),
            'tahun_perolehan' => 2026,
            'no_register_int' => 1,
            'no_register' => $nibarKdp,
            'nibar' => $nibarKdp,
            'qr_code_path' => "/scan/{$nibarKdp}",
            'ruang_pemegang' => $ruangKdp,
            'kondisi' => 'Rusak Berat',
            'status' => 'Tersedia'
        ]);

        // 9. ATB: Software SIMRS Terintegrasi & EMR Cloud (1 Lisensi)
        $astapAtb = Astap::create([
            'jenis_pengadaan_id' => $getJenisPengadaanId('0003'),
            'rekening_belanja_id' => $getRekeningId('5.2.06.01'),
            'jenis_astap_id' => $getJenisAstapId('1.5.3.01.01.01.005'),
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

        $nibarAtb = '120135110200000028000020241530101010050000001';
        $ruangAtb = 'Instalasi IT & SIMRS RSUD';
        AstapRegister::create([
            'astap_id' => $astapAtb->id,
            'unit_id' => $resolveUnitId($ruangAtb),
            'tahun_perolehan' => 2024,
            'no_register_int' => 1,
            'no_register' => $nibarAtb,
            'nibar' => $nibarAtb,
            'qr_code_path' => "/scan/{$nibarAtb}",
            'ruang_pemegang' => $ruangAtb,
            'kondisi' => 'Baik',
            'status' => 'Tersedia'
        ]);

        // 10. EXTRACOM: Gunting Angkat Jahitan Littauer 14cm (10 Pcs < Rp 300rb)
        $astapGunting = Astap::create([
            'jenis_pengadaan_id' => $getJenisPengadaanId('0012'),
            'rekening_belanja_id' => $getRekeningId('5.2.02.02'),
            'jenis_astap_id' => $getJenisAstapId('1.3.2.02.01.01.099'),
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
            $nibar = "12013511020000002800002026132020101099{$noRegStr}";
            $ruangGunting = ($i == 1) ? 'IGD & Poliklinik Bedah' : null;
            AstapRegister::create([
                'astap_id' => $astapGunting->id,
                'unit_id' => $resolveUnitId($ruangGunting),
                'tahun_perolehan' => 2026,
                'no_register_int' => $i,
                'no_register' => $nibar,
                'nibar' => $nibar,
                'qr_code_path' => "/scan/{$nibar}",
                'ruang_pemegang' => $ruangGunting,
                'kondisi' => 'Baik',
                'status' => 'Tersedia'
            ]);
        }

        // 11. EXTRACOM: Timbangan Bayi Analog (5 Unit < Rp 300rb)
        $astapTimbangan = Astap::create([
            'jenis_pengadaan_id' => $getJenisPengadaanId('0012'),
            'rekening_belanja_id' => $getRekeningId('5.2.02.02'),
            'jenis_astap_id' => $getJenisAstapId('1.3.2.02.01.02.045'),
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

        $ruangTimbangan = 'Paviliun Anak & Perinatologi';
        for ($i = 1; $i <= 5; $i++) {
            $noRegStr = str_pad($i, 7, '0', STR_PAD_LEFT);
            $nibar = "12013511020000002800002026132020102045{$noRegStr}";
            $kondisiSample = ($i <= 3) ? 'Baik' : 'Rusak Ringan';
            AstapRegister::create([
                'astap_id' => $astapTimbangan->id,
                'unit_id' => $resolveUnitId($ruangTimbangan),
                'tahun_perolehan' => 2026,
                'no_register_int' => $i,
                'no_register' => $nibar,
                'nibar' => $nibar,
                'qr_code_path' => "/scan/{$nibar}",
                'ruang_pemegang' => $ruangTimbangan,
                'kondisi' => $kondisiSample,
                'status' => 'Tersedia'
            ]);
        }
    }
}
