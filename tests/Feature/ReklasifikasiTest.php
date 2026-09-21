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
}
