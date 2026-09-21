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
            'keterangan' => 'Pengujian Reklasifikasi Otomatis',
        ];

        $response = $this->actingAs($admin)->postJson(route('master.reklasifikasi.store'), $payload);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('astap_reklasis', [
            'astap_id' => $astap->id,
            'nomor_ba_reklas' => 'BA/REKLAS/TEST/001',
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
}


