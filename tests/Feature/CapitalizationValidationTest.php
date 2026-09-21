<?php

namespace Tests\Feature;

use App\Models\Astap;
use App\Models\JenisAstap;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CapitalizationValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $this->admin = User::first() ?? User::factory()->create(['role' => 'master_admin']);
    }

    public function test_tanah_subtotal_under_300k_is_accepted_as_intrakomptabel()
    {
        $payload = [
            'nama_barang' => 'Tanah Pekarangan Pos',
            'tahun_perolehan' => 2026,
            'jenis_aset_kode' => '1.3.1',
            'is_extracomtable' => 0,
            'jumlah_anggaran' => 250000,
            'tanah_items' => [
                [
                    'tanah_nama_barang' => 'Bidang Tanah Kecil',
                    'tanah_luas' => 50,
                    'tanah_alamat' => 'Jl. Pahlawan',
                    'tanah_nilai_fisik' => 150000,
                    'tanah_nilai_perencanaan' => 50000,
                    'tanah_nilai_pengawasan' => 0,
                ]
            ],
            'nama_rekanan' => 'CV Mitra Sejahtera',
            'bentuk_rekanan' => 'CV',
            'nomor_spk' => 'SPK-001-TEST',
            'nomor_bap' => 'BAP-001-TEST',
            'nomor_bast' => 'BAST-001-TEST',
        ];

        $response = $this->actingAs($this->admin)->postJson(route('astap.store'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $astap = Astap::latest('id')->first();
        $this->assertNotNull($astap);
        $this->assertEquals(0, $astap->is_extracomtable);
        $this->assertEquals('KIB A', $astap->category);
        $this->assertEquals(200000, $astap->total_realisasi);
    }

    public function test_tanah_with_zero_subtotal_is_rejected()
    {
        $payload = [
            'nama_barang' => 'Tanah Nol Rupiah',
            'tahun_perolehan' => 2026,
            'jenis_aset_kode' => '1.3.1',
            'is_extracomtable' => 0,
            'jumlah_anggaran' => 100000,
            'tanah_items' => [
                [
                    'tanah_nama_barang' => 'Bidang Tanah Kosong',
                    'tanah_luas' => 50,
                    'tanah_alamat' => 'Jl. Pahlawan',
                    'tanah_nilai_fisik' => 0,
                    'tanah_nilai_perencanaan' => 0,
                    'tanah_nilai_pengawasan' => 0,
                ]
            ],
            'nama_rekanan' => 'CV Mitra Sejahtera',
            'bentuk_rekanan' => 'CV',
            'nomor_spk' => 'SPK-002-TEST',
            'nomor_bap' => 'BAP-002-TEST',
            'nomor_bast' => 'BAST-002-TEST',
        ];

        $response = $this->actingAs($this->admin)->postJson(route('astap.store'), $payload);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    public function test_atb_under_300k_is_accepted()
    {
        $payload = [
            'nama_barang' => 'Plugin WordPress Premium',
            'tahun_perolehan' => 2026,
            'jenis_aset_kode' => '1.5.3',
            'is_extracomtable' => 0,
            'jumlah_anggaran' => 100000,
            'atb_items' => [
                [
                    'atb_judul_nama' => 'Plugin WordPress Premium',
                    'atb_jumlah' => 1,
                    'atb_satuan' => 'Lisensi',
                    'atb_nilai_satuan' => 75000,
                    'atb_administrasi_proyek' => 0,
                ]
            ],
            'nama_rekanan' => 'CV Digital Creative',
            'bentuk_rekanan' => 'CV',
            'nomor_spk' => 'SPK-ATB-001',
            'nomor_bap' => 'BAP-ATB-001',
            'nomor_bast' => 'BAST-ATB-001',
        ];

        $response = $this->actingAs($this->admin)->postJson(route('astap.store'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $astap = Astap::latest('id')->first();
        $this->assertNotNull($astap);
        $this->assertEquals(0, $astap->is_extracomtable);
        $this->assertEquals('ATB', $astap->category);
        $this->assertEquals(75000, $astap->total_realisasi);
    }

    public function test_mesin_regular_under_300k_is_rejected_and_requires_extracom()
    {
        $payload = [
            'nama_barang' => 'Kabel USB Tester',
            'tahun_perolehan' => 2026,
            'jenis_aset_kode' => '1.3.2',
            'is_extracomtable' => 0,
            'jumlah_anggaran' => 100000,
            'mesin_items' => [
                [
                    'mesin_nama_barang' => 'Kabel USB Tester',
                    'mesin_nilai_satuan' => 150000,
                    'mesin_volume' => 1,
                    'mesin_satuan' => 'Unit',
                ]
            ],
            'nama_rekanan' => 'Toko Elektronik Maju',
            'bentuk_rekanan' => 'Toko',
            'nomor_spk' => 'SPK-003-TEST',
            'nomor_bap' => 'BAP-003-TEST',
            'nomor_bast' => 'BAST-003-TEST',
        ];

        $response = $this->actingAs($this->admin)->postJson(route('astap.store'), $payload);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    public function test_mesin_regular_over_300k_is_accepted()
    {
        $payload = [
            'nama_barang' => 'Laptop Thinkpad',
            'tahun_perolehan' => 2026,
            'jenis_aset_kode' => '1.3.2',
            'is_extracomtable' => 0,
            'jumlah_anggaran' => 15000000,
            'mesin_items' => [
                [
                    'mesin_nama_barang' => 'Laptop Thinkpad',
                    'mesin_nilai_satuan' => 12500000,
                    'mesin_volume' => 1,
                    'mesin_satuan' => 'Unit',
                ]
            ],
            'nama_rekanan' => 'Toko Komputer Sukses',
            'bentuk_rekanan' => 'Toko',
            'nomor_spk' => 'SPK-004-TEST',
            'nomor_bap' => 'BAP-004-TEST',
            'nomor_bast' => 'BAST-004-TEST',
        ];

        $response = $this->actingAs($this->admin)->postJson(route('astap.store'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }
}
