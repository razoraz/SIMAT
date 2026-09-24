<?php

namespace Tests\Feature;

use App\Models\Astap;
use App\Models\AstapReklas;
use App\Models\JenisAstap;
use App\Models\JenisReklasifikasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReklasifikasiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\JenisReklasifikasiSeeder::class);
    }

    public function test_admin_can_access_master_reklasifikasi()
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'master_admin']);

        $response = $this->actingAs($admin)->get(route('master.reklasifikasi'));
        $response->assertStatus(200);
        $response->assertSee('Reklasifikasi Aset Tetap');
        $response->assertSee('Matriks Neraca Reklasifikasi Aset Tetap');
    }

    public function test_can_store_and_destroy_reklasifikasi()
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'master_admin']);
        
        $astap = Astap::first() ?? Astap::create([
            'nama_barang' => 'Tractor Test',
            'tahun_perolehan' => 2026,
            'jumlah_volume' => 1,
            'harga_satuan' => 50000000,
            'total_realisasi' => 50000000,
            'jumlah_anggaran' => 50000000,
            'user_id' => $admin->id,
        ]);

        $asalRow = JenisReklasifikasi::where('kode_prefix', '1.3.2.01')->first() ?? JenisReklasifikasi::first();
        $tujuanRow = JenisReklasifikasi::where('kode_prefix', '1.3.3.01')->first() ?? JenisReklasifikasi::skip(1)->first();

        // 1. Simpan Transaksi Reklasifikasi
        $payload = [
            'astap_id' => $astap->id,
            'jenis_reklas' => 'KOREKSI_REKENING',
            'jenis_reklasifikasi_asal_id' => $asalRow->id,
            'jenis_reklasifikasi_tujuan_id' => $tujuanRow->id,
            'asal_kib' => 'KIB B',
            'tujuan_kib' => 'KIB C',
            'nilai_reklas' => 5000000,
            'tanggal_reklas' => '2026-03-20',
            'triwulan' => 1,
            'tahun' => 2026,
            'nomor_ba_reklas' => 'BA/REKLAS/TEST/001',
            'alasan_reklas' => 'Salah input rekening saat pengadaan barang',
            'keterangan' => 'Pengujian Reklasifikasi Otomatis',
        ];

        $response = $this->actingAs($admin)->postJson(route('master.reklasifikasi.store'), $payload);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('astap_reklasis', [
            'astap_id' => $astap->id,
            'nomor_ba_reklas' => 'BA/REKLAS/TEST/001',
            'alasan_reklas' => 'Salah input rekening saat pengadaan barang',
        ]);

        $astap->refresh();
        $this->assertTrue((bool)$astap->is_reklas);

        // 2. Hapus Transaksi Reklasifikasi
        $reklas = AstapReklas::where('nomor_ba_reklas', 'BA/REKLAS/TEST/001')->first();
        $this->assertNotNull($reklas);

        $delResponse = $this->actingAs($admin)->deleteJson(route('master.reklasifikasi.destroy', $reklas->id));
        $delResponse->assertStatus(200);
        $delResponse->assertJson(['success' => true]);

        $this->assertDatabaseMissing('astap_reklasis', [
            'id' => $reklas->id,
        ]);
    }

    public function test_can_reklas_to_extracom_with_multi_items_and_custom_unit_prices()
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'master_admin']);

        $astap = Astap::create([
            'nama_barang' => 'Paket Perlengkapan Medis',
            'tahun_perolehan' => 2026,
            'jumlah_volume' => 5,
            'harga_satuan' => 400000,
            'total_realisasi' => 2000000,
            'jumlah_anggaran' => 2000000,
            'user_id' => $admin->id,
            'spesifikasi_json' => [
                'mesin_items' => [
                    [
                        'mesin_nama_barang' => 'Stetoskop Medis',
                        'mesin_jumlah_barang' => 2,
                        'mesin_satuan' => 'Unit',
                        'mesin_nilai_satuan' => 400000,
                    ],
                    [
                        'mesin_nama_barang' => 'Tensimeter Digital',
                        'mesin_jumlah_barang' => 3,
                        'mesin_satuan' => 'Unit',
                        'mesin_nilai_satuan' => 400000,
                    ]
                ]
            ]
        ]);

        // Simpan reklasifikasi ke Ekstrakomptabel dengan 2 harga satuan berbeda (< 300rb)
        $payload = [
            'astap_id' => $astap->id,
            'jenis_reklas' => 'EKSTRAKOMPTABEL',
            'asal_kib' => 'KIB B',
            'tujuan_kib' => 'EKSTRAKOMPTABEL',
            'nilai_reklas' => (2 * 150000) + (3 * 200000), // 300.000 + 600.000 = 900.000
            'tanggal_reklas' => '2026-03-21',
            'triwulan' => 1,
            'tahun' => 2026,
            'nomor_ba_reklas' => 'BA/REKLAS/EXTRACOM/001',
            'keterangan' => 'Reklasifikasi 2 barang berbeda ke Ekstrakomptabel',
            'reklas_items' => [
                [
                    'nama_barang' => 'Stetoskop Medis',
                    'jumlah_volume' => 2,
                    'satuan' => 'Unit',
                    'harga_satuan' => 150000,
                ],
                [
                    'nama_barang' => 'Tensimeter Digital',
                    'jumlah_volume' => 3,
                    'satuan' => 'Unit',
                    'harga_satuan' => 200000,
                ]
            ]
        ];

        $response = $this->actingAs($admin)->postJson(route('master.reklasifikasi.store'), $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'astap' => [
                'is_extracomtable' => true,
                'category' => 'EXTRACOM',
                'is_reklas' => true,
                'jenis_reklas' => 'EKSTRAKOMPTABEL',
                'total_realisasi_num' => 900000,
            ]
        ]);

        $astap->refresh();
        $this->assertTrue((bool)$astap->is_extracomtable);
        $this->assertTrue((bool)$astap->is_reklas);
        $this->assertEquals('EKSTRAKOMPTABEL', $astap->jenis_reklas);
        $this->assertEquals(900000, (float)$astap->total_realisasi);

        // Verifikasi spesifikasi_json ter-update untuk 2 barang tersebut
        $spec = $astap->spesifikasi_json;
        $this->assertCount(2, $spec['mesin_items']);
        $this->assertEquals(150000, (float)$spec['mesin_items'][0]['mesin_nilai_satuan']);
        $this->assertEquals(200000, (float)$spec['mesin_items'][1]['mesin_nilai_satuan']);
    }

    public function test_reklas_extracom_rejects_unit_price_above_300000()
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'master_admin']);

        $astap = Astap::create([
            'nama_barang' => 'Alat Kesehatan Mahal',
            'tahun_perolehan' => 2026,
            'jumlah_volume' => 1,
            'harga_satuan' => 500000,
            'total_realisasi' => 500000,
            'jumlah_anggaran' => 500000,
            'user_id' => $admin->id,
        ]);

        $payload = [
            'astap_id' => $astap->id,
            'jenis_reklas' => 'EKSTRAKOMPTABEL',
            'asal_kib' => 'KIB B',
            'tujuan_kib' => 'EKSTRAKOMPTABEL',
            'nilai_reklas' => 350000,
            'tanggal_reklas' => '2026-03-21',
            'triwulan' => 1,
            'tahun' => 2026,
            'reklas_items' => [
                [
                    'nama_barang' => 'Alat Kesehatan Mahal',
                    'jumlah_volume' => 1,
                    'satuan' => 'Unit',
                    'harga_satuan' => 350000, // > 300.000
                ]
            ]
        ];

        $response = $this->actingAs($admin)->postJson(route('master.reklasifikasi.store'), $payload);
        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
        $response->assertSee('melebihi batas Ekstrakomptabel (Maksimal Rp 300.000)');
    }

    public function test_can_reklas_extracom_to_intracom_kapitalisasi_with_multi_items()
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'master_admin']);

        // Aset awal berstatus Ekstrakomptabel
        $astap = Astap::create([
            'nama_barang' => 'Perangkat Diagnostik',
            'tahun_perolehan' => 2026,
            'jumlah_volume' => 2,
            'harga_satuan' => 250000,
            'total_realisasi' => 500000,
            'jumlah_anggaran' => 500000,
            'user_id' => $admin->id,
            'is_extracomtable' => true,
            'category' => 'EXTRACOM',
            'spesifikasi_json' => [
                'mesin_items' => [
                    [
                        'mesin_nama_barang' => 'Diagnostik Pro A',
                        'mesin_jumlah_barang' => 1,
                        'mesin_satuan' => 'Unit',
                        'mesin_nilai_satuan' => 250000,
                    ],
                    [
                        'mesin_nama_barang' => 'Diagnostik Pro B',
                        'mesin_jumlah_barang' => 1,
                        'mesin_satuan' => 'Unit',
                        'mesin_nilai_satuan' => 250000,
                    ]
                ]
            ]
        ]);

        $payload = [
            'astap_id' => $astap->id,
            'jenis_reklas' => 'KAPITALISASI_INTRAKOM',
            'asal_kib' => 'EKSTRAKOMPTABEL',
            'tujuan_kib' => 'KIB B',
            'nilai_reklas' => 800000,
            'tanggal_reklas' => '2026-03-21',
            'triwulan' => 1,
            'tahun' => 2026,
            'nomor_ba_reklas' => 'BA/KAPITALISASI/001',
            'keterangan' => 'Kapitalisasi balik ke Intrakomptabel KIB B',
            'reklas_items' => [
                [
                    'nama_barang' => 'Diagnostik Pro A',
                    'jumlah_volume' => 1,
                    'satuan' => 'Unit',
                    'harga_satuan' => 350000, // > 300.000
                ],
                [
                    'nama_barang' => 'Diagnostik Pro B',
                    'jumlah_volume' => 1,
                    'satuan' => 'Unit',
                    'harga_satuan' => 450000, // > 300.000
                ]
            ]
        ];

        $response = $this->actingAs($admin)->postJson(route('master.reklasifikasi.store'), $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'astap' => [
                'is_extracomtable' => false,
                'category' => 'KIB B',
                'is_reklas' => true,
                'jenis_reklas' => 'KAPITALISASI_INTRAKOM',
                'total_realisasi_num' => 800000,
            ]
        ]);

        $astap->refresh();
        $this->assertFalse((bool)$astap->is_extracomtable);
        $this->assertEquals('KIB B', $astap->category);
        $this->assertTrue((bool)$astap->is_reklas);
        $this->assertEquals('KAPITALISASI_INTRAKOM', $astap->jenis_reklas);
        $this->assertEquals(800000, (float)$astap->total_realisasi);

        $spec = $astap->spesifikasi_json;
        $this->assertCount(2, $spec['mesin_items']);
        $this->assertEquals(350000, (float)$spec['mesin_items'][0]['mesin_nilai_satuan']);
        $this->assertEquals(450000, (float)$spec['mesin_items'][1]['mesin_nilai_satuan']);
    }

    public function test_reklas_intracom_rejects_unit_price_300000_or_below()
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'master_admin']);

        $astap = Astap::create([
            'nama_barang' => 'Barang Ekstrakom Murah',
            'tahun_perolehan' => 2026,
            'jumlah_volume' => 1,
            'harga_satuan' => 100000,
            'total_realisasi' => 100000,
            'jumlah_anggaran' => 100000,
            'user_id' => $admin->id,
            'is_extracomtable' => true,
            'category' => 'EXTRACOM',
        ]);

        $payload = [
            'astap_id' => $astap->id,
            'jenis_reklas' => 'KAPITALISASI_INTRAKOM',
            'asal_kib' => 'EKSTRAKOMPTABEL',
            'tujuan_kib' => 'KIB B',
            'nilai_reklas' => 250000,
            'tanggal_reklas' => '2026-03-21',
            'triwulan' => 1,
            'tahun' => 2026,
            'reklas_items' => [
                [
                    'nama_barang' => 'Barang Ekstrakom Murah',
                    'jumlah_volume' => 1,
                    'satuan' => 'Unit',
                    'harga_satuan' => 250000, // <= 300.000
                ]
            ]
        ];

        $response = $this->actingAs($admin)->postJson(route('master.reklasifikasi.store'), $payload);
        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
        $response->assertSee('harus lebih dari Rp 300.000 untuk masuk ke Intrakomptabel');
    }

    public function test_can_reklas_kdp_to_definitif_kib_c()
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'master_admin']);

        $jenisKdp = JenisAstap::firstOrCreate(
            ['jenis' => '1.3.6.01'],
            [
                'nama_jenis' => 'Konstruksi Dalam Pengerjaan',
                'sub_rincian_objek' => '1.3.6.01.01',
                'uraian_sub_rincian' => 'Konstruksi Dalam Pengerjaan',
                'sub_sub_rincian_objek' => '1.3.6.01.01.01',
                'uraian_sub_sub_rincian' => 'Konstruksi Dalam Pengerjaan',
            ]
        );
        $jenisKibC = JenisAstap::firstOrCreate(
            ['jenis' => '1.3.3.01'],
            [
                'nama_jenis' => 'Gedung dan Bangunan',
                'sub_rincian_objek' => '1.3.3.01.01',
                'uraian_sub_rincian' => 'Gedung dan Bangunan',
                'sub_sub_rincian_objek' => '1.3.3.01.01.01',
                'uraian_sub_sub_rincian' => 'Gedung dan Bangunan',
            ]
        );

        $astap = Astap::create([
            'nama_barang' => 'Pembangunan Gedung Paviliun VIP',
            'tahun_perolehan' => 2026,
            'jumlah_volume' => 1,
            'harga_satuan' => 1500000000,
            'total_realisasi' => 1500000000,
            'jumlah_anggaran' => 1500000000,
            'user_id' => $admin->id,
            'jenis_astap_id' => $jenisKdp->id,
        ]);

        $payload = [
            'astap_id' => $astap->id,
            'jenis_reklas' => 'KDP_TO_DEFINITIF',
            'asal_kib' => 'KIB F',
            'tujuan_kib' => 'KIB C',
            'nilai_reklas' => 1500000000,
            'tanggal_reklas' => '2026-03-21',
            'triwulan' => 1,
            'tahun' => 2026,
            'nomor_ba_reklas' => 'BA/KDP-SELESAI/001',
            'keterangan' => 'Kapitalisasi KDP selesai fisik 100%',
        ];

        $response = $this->actingAs($admin)->postJson(route('master.reklasifikasi.store'), $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'astap' => [
                'category' => 'KIB C',
                'is_reklas' => true,
                'jenis_reklas' => 'KDP_TO_DEFINITIF',
            ]
        ]);

        $astap->refresh();
        $this->assertEquals('KIB C', $astap->category);
        $this->assertTrue((bool)$astap->is_reklas);
    }

    public function test_can_reklas_definitif_to_kdp()
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'master_admin']);

        $jenisKibD = JenisAstap::firstOrCreate(
            ['jenis' => '1.3.4.01'],
            [
                'nama_jenis' => 'Jalan, Jaringan dan Irigasi',
                'sub_rincian_objek' => '1.3.4.01.01',
                'uraian_sub_rincian' => 'Jalan, Jaringan dan Irigasi',
                'sub_sub_rincian_objek' => '1.3.4.01.01.01',
                'uraian_sub_sub_rincian' => 'Jalan, Jaringan dan Irigasi',
            ]
        );
        $jenisKdp = JenisAstap::firstOrCreate(
            ['jenis' => '1.3.6.01'],
            [
                'nama_jenis' => 'Konstruksi Dalam Pengerjaan',
                'sub_rincian_objek' => '1.3.6.01.01',
                'uraian_sub_rincian' => 'Konstruksi Dalam Pengerjaan',
                'sub_sub_rincian_objek' => '1.3.6.01.01.01',
                'uraian_sub_sub_rincian' => 'Konstruksi Dalam Pengerjaan',
            ]
        );

        $astap = Astap::create([
            'nama_barang' => 'Pembangunan Saluran IPAL Baru',
            'tahun_perolehan' => 2026,
            'jumlah_volume' => 1,
            'harga_satuan' => 400000000,
            'total_realisasi' => 400000000,
            'jumlah_anggaran' => 400000000,
            'user_id' => $admin->id,
            'jenis_astap_id' => $jenisKibD->id,
        ]);

        $payload = [
            'astap_id' => $astap->id,
            'jenis_reklas' => 'DEFINITIF_TO_KDP',
            'asal_kib' => 'KIB D',
            'tujuan_kib' => 'KIB F',
            'nilai_reklas' => 400000000,
            'tanggal_reklas' => '2026-03-21',
            'triwulan' => 1,
            'tahun' => 2026,
            'nomor_ba_reklas' => 'BA/START-KDP/001',
            'keterangan' => 'Pengalihan belanja fisik baru ke KDP KIB F',
        ];

        $response = $this->actingAs($admin)->postJson(route('master.reklasifikasi.store'), $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'astap' => [
                'category' => 'KIB F',
                'is_reklas' => true,
                'jenis_reklas' => 'DEFINITIF_TO_KDP',
            ]
        ]);

        $astap->refresh();
        $this->assertEquals('KIB F', $astap->category);
        $this->assertTrue((bool)$astap->is_reklas);
    }

    public function test_can_reklas_koreksi_nilai_audit_bpk_kurang()
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'master_admin']);

        $astap = Astap::create([
            'nama_barang' => 'Mesin Anaesthesia Carestation',
            'tahun_perolehan' => 2026,
            'jumlah_volume' => 1,
            'harga_satuan' => 45000000,
            'total_realisasi' => 45000000,
            'jumlah_anggaran' => 45000000,
            'user_id' => $admin->id,
        ]);

        // Koreksi nilai audit BPK berkurang Rp 5.000.000 karena salah hitung pajak / biaya administrasi
        $payload = [
            'astap_id' => $astap->id,
            'jenis_reklas' => 'KOREKSI_LAIN',
            'tipe_koreksi' => 'kurang',
            'nilai_reklas' => 5000000,
            'tanggal_reklas' => '2026-03-21',
            'triwulan' => 1,
            'tahun' => 2026,
            'nomor_ba_reklas' => 'LHP-BPK/2026/04/RSDK',
            'keterangan' => 'Koreksi nilai audit BPK atas kelebihan beban administrasi',
        ];

        $response = $this->actingAs($admin)->postJson(route('master.reklasifikasi.store'), $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'astap' => [
                'total_realisasi_num' => 40000000,
                'is_reklas' => true,
                'jenis_reklas' => 'KOREKSI_LAIN',
            ]
        ]);

        $astap->refresh();
        $this->assertEquals(40000000, (float)$astap->total_realisasi);
        $this->assertEquals(40000000, (float)$astap->harga_satuan);
        $this->assertTrue((bool)$astap->is_reklas);
        $this->assertEquals('KOREKSI_LAIN', $astap->jenis_reklas);
    }

    public function test_can_reklas_pindah_kib_rekening()
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'master_admin']);

        $jenisB = JenisAstap::firstOrCreate(
            ['jenis' => '1.3.2.05'],
            [
                'nama_jenis' => 'Peralatan dan Mesin',
                'sub_rincian_objek' => '1.3.2.05.01',
                'uraian_sub_rincian' => 'Alat Rumah Tangga',
                'sub_sub_rincian_objek' => '1.3.2.05.01.01',
                'uraian_sub_sub_rincian' => 'Alat Rumah Tangga',
            ]
        );

        $astap = Astap::create([
            'nama_barang' => 'Instalasi Panel Listrik Sentral',
            'tahun_perolehan' => 2026,
            'jumlah_volume' => 1,
            'harga_satuan' => 25000000,
            'total_realisasi' => 25000000,
            'jumlah_anggaran' => 25000000,
            'user_id' => $admin->id,
            'jenis_astap_id' => $jenisB->id,
        ]);

        // Pindah KIB dari KIB B ke KIB D (Jaringan/Instalasi)
        $payload = [
            'astap_id' => $astap->id,
            'jenis_reklas' => 'KOREKSI_REKENING',
            'asal_kib' => 'KIB B',
            'tujuan_kib' => 'KIB D',
            'nilai_reklas' => 25000000,
            'tanggal_reklas' => '2026-03-21',
            'triwulan' => 1,
            'tahun' => 2026,
            'nomor_ba_reklas' => 'BA/PINDAH-KIB/002',
            'keterangan' => 'Pindah KIB B ke KIB D penyesuaian kodefikasi 108',
        ];

        $response = $this->actingAs($admin)->postJson(route('master.reklasifikasi.store'), $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'astap' => [
                'is_reklas' => true,
                'jenis_reklas' => 'KOREKSI_REKENING',
            ]
        ]);

        $astap->refresh();
        $this->assertTrue((bool)$astap->is_reklas);
        $this->assertEquals('KOREKSI_REKENING', $astap->jenis_reklas);
    }

    public function test_can_reklas_koreksi_nilai_with_multi_items_and_anggaran_adjustment()
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'master_admin']);

        // Aset dengan 2 rincian barang: Barang 1 (25jt) & Barang 2 (20jt) -> Total 45jt
        $astap = Astap::create([
            'nama_barang' => 'Paket Alat Diagnostik Rawat Jalan',
            'tahun_perolehan' => 2026,
            'jumlah_volume' => 2,
            'harga_satuan' => 22500000,
            'total_realisasi' => 45000000,
            'jumlah_anggaran' => 50000000,
            'user_id' => $admin->id,
            'spesifikasi_json' => [
                'mesin_items' => [
                    [
                        'mesin_nama_barang' => 'USG Portable Unit',
                        'mesin_jumlah_barang' => 1,
                        'mesin_satuan' => 'Unit',
                        'mesin_nilai_satuan' => 25000000,
                        'mesin_total_nilai' => 25000000,
                    ],
                    [
                        'mesin_nama_barang' => 'Trolley Stand & Aksesoris',
                        'mesin_jumlah_barang' => 1,
                        'mesin_satuan' => 'Unit',
                        'mesin_nilai_satuan' => 20000000,
                        'mesin_total_nilai' => 20000000,
                    ],
                ]
            ],
        ]);

        // Temuan audit BPK: Barang 1 salah kapitalisasi biaya kirim/pajak Rp 5.000.000 (menjadi 20jt)
        // Nilai realisasi baru otomatis menjadi: (1 * 20jt) + (1 * 20jt) = 40jt
        // Selisih koreksi: 5jt berkurang. Nilai anggaran disesuaikan menjadi 45jt.
        $payload = [
            'astap_id' => $astap->id,
            'jenis_reklas' => 'KOREKSI_LAIN',
            'tipe_koreksi' => 'kurang',
            'nilai_reklas' => 5000000,
            'jumlah_anggaran' => 45000000,
            'tanggal_reklas' => '2026-03-22',
            'triwulan' => 1,
            'tahun' => 2026,
            'nomor_ba_reklas' => 'LHP-BPK/2026/04/AUDIT',
            'keterangan' => 'Koreksi nilai kapitalisasi barang 1 hasil temuan BPK',
            'reklas_items' => [
                [
                    'nama_barang' => 'USG Portable Unit',
                    'jumlah_volume' => 1,
                    'satuan' => 'Unit',
                    'harga_satuan' => 20000000,
                ],
                [
                    'nama_barang' => 'Trolley Stand & Aksesoris',
                    'jumlah_volume' => 1,
                    'satuan' => 'Unit',
                    'harga_satuan' => 20000000,
                ],
            ],
        ];

        $response = $this->actingAs($admin)->postJson(route('master.reklasifikasi.store'), $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'astap' => [
                'total_realisasi_num' => 40000000,
                'jumlah_anggaran' => 45000000,
                'is_reklas' => true,
                'jenis_reklas' => 'KOREKSI_LAIN',
            ]
        ]);

        $astap->refresh();
        $this->assertEquals(40000000, (float)$astap->total_realisasi);
        $this->assertEquals(45000000, (float)$astap->jumlah_anggaran);
        $this->assertTrue((bool)$astap->is_reklas);
        $this->assertEquals('KOREKSI_LAIN', $astap->jenis_reklas);

        // Verifikasi spesifikasi_json terupdate
        $spec = $astap->spesifikasi_json;
        $this->assertEquals(20000000, $spec['mesin_items'][0]['mesin_nilai_satuan']);
        $this->assertEquals(20000000, $spec['mesin_items'][0]['mesin_total_nilai']);
        $this->assertEquals(20000000, $spec['mesin_items'][1]['mesin_nilai_satuan']);
        $this->assertEquals(20000000, $spec['mesin_items'][1]['mesin_total_nilai']);

        // Verifikasi tabel transaksi reklasifikasi tercatat
        $this->assertDatabaseHas('astap_reklasis', [
            'astap_id' => $astap->id,
            'jenis_reklas' => 'KOREKSI_LAIN',
            'nilai_reklas' => 5000000,
            'nomor_ba_reklas' => 'LHP-BPK/2026/04/AUDIT',
        ]);
    }

    public function test_master_reklasifikasi_with_spesifikasi_baru_and_audit_trail_snapshot()
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'master_admin']);

        // 1. Buat Aset KIB B
        $astap = Astap::create([
            'nama_barang' => 'Kompresor Gas Medis Sentral',
            'tahun_perolehan' => 2026,
            'jumlah_volume' => 1,
            'harga_satuan' => 75000000,
            'total_realisasi' => 75000000,
            'jumlah_anggaran' => 75000000,
            'satuan' => 'Unit',
            'category' => 'KIB B',
            'merk_type' => 'Atlas Copco GA-11',
            'alamat_barang' => 'Instalasi Pemeliharaan Sarana RS (IPSRS)',
            'spesifikasi_json' => [
                'merk' => 'Atlas Copco',
                'type' => 'GA-11',
                'no_pabrik' => 'SN-COMP-2026-99',
                'spk_nomor' => 'SPK-MEDIS-2026',
            ],
            'user_id' => $admin->id,
        ]);

        // Pastikan halaman Master Reklasifikasi memuat aset dan dbMaster108
        $this->withoutExceptionHandling();
        $pageResponse = $this->actingAs($admin)->get(route('master.reklasifikasi'));
        $pageResponse->assertStatus(200);
        $pageResponse->assertViewHas('dbMaster108');
        $pageResponse->assertViewHas('kandidatAstaps');
        $pageResponse->assertSee('Rincian Audit Reklasifikasi');
        $pageResponse->assertSee('Perbandingan Spesifikasi Fisik');

        // 2. Submit Reklasifikasi Baru dari Master dengan Spesifikasi Fisik Baru (KIB B -> KIB C Gedung)
        $payload = [
            'astap_id' => $astap->id,
            'jenis_reklas' => 'KOREKSI_REKENING',
            'tujuan_kib' => 'KIB C',
            'tujuan_kode' => '1.3.3.01.01.01.001',
            'tujuan_nama' => 'Bangunan Gedung Ruang Gas Medis',
            'nilai_reklas' => 75000000,
            'tanggal_reklas' => '2026-04-10',
            'triwulan' => 2,
            'tahun' => 2026,
            'nomor_ba_reklas' => '000.2.3/BA-REKLAS/RSUD/IV/2026',
            'keterangan' => 'Koreksi salah kamar dari peralatan mesin ke bangunan instalasi permanen gas medis',
            'spesifikasi_baru' => [
                'gedung_konstruksi_bertingkat' => 'Tidak Bertingkat',
                'gedung_konstruksi_beton' => 'Beton',
                'gedung_luas_lantai_m2' => 85.5,
                'gedung_dokumen_nomor' => '640/PBG/2026/RSUD',
                'gedung_dokumen_tgl' => '2026-04-01',
                'gedung_status_tanah' => 'Tanah Pemda',
                'gedung_alamat' => 'Kompleks Sentral Gas Medis RSUD dr. H. Koesnandi',
            ],
        ];

        $response = $this->actingAs($admin)->postJson(route('master.reklasifikasi.store'), $payload);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // 3. Verifikasi Data ASTAP Terupdate
        $astap->refresh();
        $this->assertTrue((bool)$astap->is_reklas);
        $this->assertEquals('KOREKSI_REKENING', $astap->jenis_reklas);
        $this->assertEquals('Gedung / Unit', $astap->satuan);
        $this->assertEquals('Kompleks Sentral Gas Medis RSUD dr. H. Koesnandi', $astap->alamat_barang);

        $newAstapSpec = $astap->spesifikasi_json;
        $this->assertArrayHasKey('gedung_items', $newAstapSpec);
        $this->assertEquals(85.5, $newAstapSpec['gedung_items'][0]['gedung_luas_lantai_m2']);
        $this->assertEquals('Beton', $newAstapSpec['gedung_items'][0]['gedung_konstruksi_beton']);
        // Pastikan administrasi lama (SPK) dipertahankan
        $this->assertEquals('SPK-MEDIS-2026', $newAstapSpec['spk_nomor']);

        // 4. Verifikasi Audit Trail Snapshot di tabel astap_reklasis
        $reklasRecord = AstapReklas::where('astap_id', $astap->id)->first();
        $this->assertNotNull($reklasRecord);
        $this->assertEquals('KIB C', $reklasRecord->tujuan_kib);
        $this->assertEquals(75000000, (float)$reklasRecord->nilai_reklas);

        // Snapshot Lama harus merekam data KIB B (Merk Atlas Copco)
        $this->assertNotNull($reklasRecord->spesifikasi_lama);
        $this->assertEquals('Atlas Copco', $reklasRecord->spesifikasi_lama['merk']);
        $this->assertEquals('GA-11', $reklasRecord->spesifikasi_lama['type']);

        // Snapshot Baru harus merekam data KIB C (Gedung)
        $this->assertNotNull($reklasRecord->spesifikasi_baru);
        $this->assertArrayHasKey('gedung_items', $reklasRecord->spesifikasi_baru);
        $this->assertEquals(85.5, $reklasRecord->spesifikasi_baru['gedung_items'][0]['gedung_luas_lantai_m2']);
    }

    public function test_reklas_extracom_records_mutasi_kurang_on_source_kib()
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'master_admin']);

        $jenisAstap = JenisAstap::firstOrCreate(
            ['sub_rincian_objek' => '1.3.2.05.01'],
            [
                'jenis' => '1.3.2.05',
                'nama_jenis' => 'Alat Kantor dan Rumah Tangga',
                'uraian_sub_rincian' => 'Alat Kantor',
                'sub_sub_rincian_objek' => '1.3.2.05.01.01.001',
                'uraian_sub_sub_rincian' => 'Kursi Kerja Besi',
            ]
        );

        $astap = Astap::create([
            'nama_barang' => 'Kursi Kerja Besi Ekonomis',
            'tahun_perolehan' => 2026,
            'triwulan' => 'TW I',
            'sp2d_tanggal' => '2026-02-15',
            'jumlah_volume' => 1,
            'harga_satuan' => 250000,
            'total_realisasi' => 250000,
            'jumlah_anggaran' => 250000,
            'jenis_astap_id' => $jenisAstap->id,
            'user_id' => $admin->id,
        ]);

        // Reklasifikasi ke Ekstrakomptabel
        $payload = [
            'astap_id' => $astap->id,
            'jenis_reklas' => 'EKSTRAKOMPTABEL',
            'asal_kib' => 'KIB B',
            'tujuan_kib' => 'EKSTRAKOMPTABEL',
            'nilai_reklas' => 250000,
            'tanggal_reklas' => '2026-03-01',
            'triwulan' => 1,
            'tahun' => 2026,
            'nomor_ba_reklas' => 'BA/REKLAS/KURANG/001',
            'reklas_items' => [
                [
                    'nama_barang' => 'Kursi Kerja Besi Ekonomis',
                    'jumlah_volume' => 1,
                    'satuan' => 'Buah',
                    'harga_satuan' => 250000,
                ]
            ]
        ];

        $postRes = $this->actingAs($admin)->postJson(route('master.reklasifikasi.store'), $payload);
        $postRes->assertStatus(200);

        // Buka halaman Reklasifikasi dan periksa matriks
        $response = $this->actingAs($admin)->get(route('master.reklasifikasi', ['tahun' => 2026, 'triwulan' => 1]));
        $response->assertStatus(200);

        $matriks = $response->viewData('matriks');
        $subtotals = $response->viewData('subtotals');
        $grandTotal = $response->viewData('grandTotal');

        // Cari baris ALAT KANTOR DAN RUMAH TANGGA (1.3.2.05)
        $rowKantor = collect($matriks)->firstWhere('kode_prefix', '1.3.2.05');
        $this->assertNotNull($rowKantor);
        $this->assertEquals(250000, $rowKantor['saldo_awal']);
        $this->assertEquals(250000, $rowKantor['mutasi_kurang'], 'Nilai extracom harus masuk ke Mutasi Kurang (-) pada baris KIB asalnya!');
        $this->assertEquals(0, $rowKantor['saldo_akhir'], 'Saldo akhir KIB asal harus berkurang sebesar mutasi kurang!');

        // Periksa subtotal KIB B
        $this->assertEquals(250000, $subtotals['KIB B']['awal']);
        $this->assertEquals(250000, $subtotals['KIB B']['kurang'], 'Subtotal KIB B harus mencatat mutasi kurang!');
        $this->assertEquals(0, $subtotals['KIB B']['akhir']);

        // Periksa Grand Total
        $this->assertEquals(250000, $grandTotal['awal']);
        $this->assertEquals(250000, $grandTotal['kurang'], 'Grand Total mutasi kurang harus mencatat Rp 250.000!');
        $this->assertEquals(0, $grandTotal['akhir']);
    }

    public function test_reklas_inter_kib_records_mutasi_kurang_and_mutasi_tambah()
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'master_admin']);

        $jenisAstap = JenisAstap::firstOrCreate(
            ['sub_rincian_objek' => '1.3.2.01.01'],
            [
                'jenis' => '1.3.2.01',
                'nama_jenis' => 'Alat Besar',
                'uraian_sub_rincian' => 'Alat Besar Darat',
                'sub_sub_rincian_objek' => '1.3.2.01.01.01.001',
                'uraian_sub_sub_rincian' => 'Tractor',
            ]
        );

        $astap = Astap::create([
            'nama_barang' => 'Tractor Reklas Test',
            'tahun_perolehan' => 2026,
            'triwulan' => 'TW I',
            'sp2d_tanggal' => '2026-02-10',
            'jumlah_volume' => 1,
            'harga_satuan' => 10000000,
            'total_realisasi' => 10000000,
            'jumlah_anggaran' => 10000000,
            'jenis_astap_id' => $jenisAstap->id,
            'user_id' => $admin->id,
        ]);

        // Reklasifikasi dari KIB B ke KIB C
        $asalRow = JenisReklasifikasi::where('kode_prefix', '1.3.2.01')->first();
        $tujuanRow = JenisReklasifikasi::where('kode_prefix', '1.3.3.01')->first();

        $payload = [
            'astap_id' => $astap->id,
            'jenis_reklas' => 'KOREKSI_REKENING',
            'jenis_reklasifikasi_asal_id' => $asalRow->id,
            'jenis_reklasifikasi_tujuan_id' => $tujuanRow->id,
            'asal_kib' => 'KIB B',
            'tujuan_kib' => 'KIB C',
            'nilai_reklas' => 10000000,
            'tanggal_reklas' => '2026-03-05',
            'triwulan' => 1,
            'tahun' => 2026,
            'nomor_ba_reklas' => 'BA/REKLAS/BC/001',
        ];

        $postRes = $this->actingAs($admin)->postJson(route('master.reklasifikasi.store'), $payload);
        $postRes->assertStatus(200);

        // Buka halaman Reklasifikasi dan periksa matriks
        $response = $this->actingAs($admin)->get(route('master.reklasifikasi', ['tahun' => 2026, 'triwulan' => 1]));
        $response->assertStatus(200);

        $matriks = $response->viewData('matriks');
        $subtotals = $response->viewData('subtotals');
        $grandTotal = $response->viewData('grandTotal');

        // KIB B (Asal)
        $rowB = collect($matriks)->firstWhere('kode_prefix', '1.3.2.01');
        $this->assertEquals(10000000, $rowB['saldo_awal']);
        $this->assertEquals(10000000, $rowB['mutasi_kurang'], 'KIB B harus mendapatkan Mutasi Kurang (-) Rp 10.000.000!');
        $this->assertEquals(0, $rowB['saldo_akhir'], 'Saldo akhir KIB B harus 0 setelah reklas keluar!');

        // KIB C (Tujuan)
        $rowC = collect($matriks)->firstWhere('kode_prefix', '1.3.3.01');
        $this->assertEquals(10000000, $rowC['mutasi_tambah'], 'KIB C harus mendapatkan Mutasi Tambah (+) Rp 10.000.000!');
        $this->assertEquals(10000000, $rowC['saldo_akhir'], 'Saldo akhir KIB C harus bertambah Rp 10.000.000!');

        // Subtotals
        $this->assertEquals(10000000, $subtotals['KIB B']['kurang']);
        $this->assertEquals(0, $subtotals['KIB B']['akhir']);
        $this->assertEquals(10000000, $subtotals['KIB C']['tambah']);
        $this->assertEquals(10000000, $subtotals['KIB C']['akhir']);

        // Grand Total: Saldo Awal 10jt, Tambah 10jt, Kurang 10jt, Akhir 10jt
        $this->assertEquals(10000000, $grandTotal['awal']);
        $this->assertEquals(10000000, $grandTotal['tambah']);
        $this->assertEquals(10000000, $grandTotal['kurang']);
        $this->assertEquals(10000000, $grandTotal['akhir']);
    }

    public function test_astap_with_extracom_flag_directly_records_mutasi_kurang_on_source_kib()
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'master_admin']);

        $jenisAstap = JenisAstap::firstOrCreate(
            ['sub_rincian_objek' => '1.3.2.10.01'],
            [
                'jenis' => '1.3.2.10',
                'nama_jenis' => 'Komputer',
                'uraian_sub_rincian' => 'Komputer Unit',
                'sub_sub_rincian_objek' => '1.3.2.10.01.01.001',
                'uraian_sub_sub_rincian' => 'Mouse USB Ekstrakomptabel',
            ]
        );

        // Aset Belanja Modal tapi bertanda is_extracomtable = true (< 300rb) tanpa transaksi reklas manual
        Astap::create([
            'nama_barang' => 'Mouse Optik Komputer',
            'tahun_perolehan' => 2026,
            'triwulan' => 'TW I',
            'jumlah_volume' => 2,
            'harga_satuan' => 75000,
            'total_realisasi' => 150000,
            'jumlah_anggaran' => 150000,
            'is_extracomtable' => true,
            'jenis_astap_id' => $jenisAstap->id,
            'user_id' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->get(route('master.reklasifikasi', ['tahun' => 2026, 'triwulan' => 1]));
        $response->assertStatus(200);

        $matriks = $response->viewData('matriks');
        $subtotals = $response->viewData('subtotals');

        // Cari baris KOMPUTER (1.3.2.10)
        $rowKomputer = collect($matriks)->firstWhere('kode_prefix', '1.3.2.10');
        $this->assertNotNull($rowKomputer);
        $this->assertEquals(150000, $rowKomputer['saldo_awal']);
        $this->assertEquals(150000, $rowKomputer['mutasi_kurang'], 'Aset bertanda is_extracomtable otomatis masuk ke Mutasi Kurang (-) pada baris KIB asalnya!');
        $this->assertEquals(0, $rowKomputer['saldo_akhir'], 'Saldo akhir harus 0 karena aset berada di bawah batas kapitalisasi!');

        $this->assertEquals(150000, $subtotals['KIB B']['kurang']);
        $this->assertEquals(0, $subtotals['KIB B']['akhir']);
    }
}



