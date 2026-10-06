<?php

namespace Tests\Feature;

use App\Models\Astap;
use App\Models\AstapReklas;
use App\Models\JenisAstap;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RmbTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_rmb_page()
    {
        $response = $this->get(route('rmb.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_access_rmb_page()
    {
        $admin = User::factory()->create(['role' => 'master_admin']);

        $response = $this->actingAs($admin)->get(route('rmb.index'));
        $response->assertStatus(200);
        $response->assertViewIs('pages.rmb.index');
        $response->assertViewHasAll(['rmbRows', 'subtotals', 'grandTotal', 'kertasKerja', 'kpis', 'selectedTahun']);
    }

    public function test_rmb_calculates_astap_belanja_modal_and_ekstrakomptabel()
    {
        $admin = User::factory()->create(['role' => 'master_admin']);

        $jenisB = JenisAstap::create([
            'jenis' => '1.3.2.05',
            'nama_jenis' => 'Peralatan dan Mesin',
            'sub_rincian_objek' => '1.3.2.05',
            'uraian_sub_rincian' => 'Alat Kantor dan Rumah Tangga',
            'sub_sub_rincian_objek' => '1.3.2.05.01.01',
            'uraian_sub_sub_rincian' => 'Meja Kerja Pejabat',
        ]);

        // Aset 1: Belanja modal intra Rp 10.000.000
        $astap1 = Astap::create([
            'nama_barang' => 'Laptop Server',
            'tahun_perolehan' => 2026,
            'triwulan' => 'TW I',
            'jumlah_volume' => 1,
            'harga_satuan' => 10000000,
            'total_realisasi' => 10000000,
            'jumlah_anggaran' => 10000000,
            'category' => 'KIB B',
            'jenis_astap_id' => $jenisB->id,
            'user_id' => $admin->id,
            'is_extracomtable' => false,
        ]);

        // Aset 2: Ekstrakomptabel Rp 250.000
        $astap2 = Astap::create([
            'nama_barang' => 'Tempat Sampah Medis',
            'tahun_perolehan' => 2026,
            'triwulan' => 'TW I',
            'jumlah_volume' => 1,
            'harga_satuan' => 250000,
            'total_realisasi' => 250000,
            'jumlah_anggaran' => 250000,
            'category' => 'KIB B',
            'jenis_astap_id' => $jenisB->id,
            'user_id' => $admin->id,
            'is_extracomtable' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('rmb.index', ['tahun' => 2026]));
        $response->assertStatus(200);

        $viewData = $response->viewData('rmbRows');
        $kertasKerja = $response->viewData('kertasKerja');

        // Kode 1.3.2.05 Alat Kantor dan Rumah Tangga
        $row = $viewData['1.3.2.05'];
        $this->assertEquals(0, $row['saldo_awal']);
        $this->assertEquals(10250000, $row['c1_belanja_modal']);
        $this->assertEquals(250000, $row['c12_kapitalisasi_kurang']); // Pengurangan ekstrakomptabel
        $this->assertEquals(10000000, $row['saldo_akhir']);

        // Kertas kerja rekonsiliasi
        $this->assertEquals(10000000, $kertasKerja['saldo_akhir_aset_tetap']);
        $this->assertEquals(250000, $kertasKerja['koreksi_ekstrakom']);
        $this->assertEquals(10250000, $kertasKerja['total_rekon_belanja']);
        $this->assertEquals(10250000, $kertasKerja['realisasi_kasda_lra']);
        $this->assertTrue((bool)$kertasKerja['is_balance']);
    }

    public function test_rmb_filters_by_triwulan()
    {
        $admin = User::factory()->create(['role' => 'master_admin']);

        $jenis = JenisAstap::create([
            'jenis' => '1.3.1.01',
            'nama_jenis' => 'Tanah',
            'sub_rincian_objek' => '1.3.1.01',
            'uraian_sub_rincian' => 'Tanah',
            'sub_sub_rincian_objek' => '1.3.1.01.01',
            'uraian_sub_sub_rincian' => 'Tanah Bangunan Gedung',
        ]);

        Astap::create([
            'nama_barang' => 'Tanah Perluasan Timur',
            'tahun_perolehan' => 2026,
            'triwulan' => 'TW I',
            'jumlah_volume' => 1,
            'harga_satuan' => 50000000,
            'total_realisasi' => 50000000,
            'jumlah_anggaran' => 50000000,
            'category' => 'KIB A',
            'jenis_astap_id' => $jenis->id,
            'user_id' => $admin->id,
        ]);

        Astap::create([
            'nama_barang' => 'Tanah Perluasan Barat',
            'tahun_perolehan' => 2026,
            'triwulan' => 'TW II',
            'jumlah_volume' => 1,
            'harga_satuan' => 30000000,
            'total_realisasi' => 30000000,
            'jumlah_anggaran' => 30000000,
            'category' => 'KIB A',
            'jenis_astap_id' => $jenis->id,
            'user_id' => $admin->id,
        ]);

        // Filter TW I -> hanya tanah perluasan timur (50jt)
        $resTw1 = $this->actingAs($admin)->get(route('rmb.index', ['tahun' => 2026, 'triwulan' => '1']));
        $resTw1->assertStatus(200);
        $rowsTw1 = $resTw1->viewData('rmbRows');
        $this->assertEquals(50000000, $rowsTw1['1.3.1.01']['c1_belanja_modal']);

        // Filter all -> total keduanya (80jt)
        $resAll = $this->actingAs($admin)->get(route('rmb.index', ['tahun' => 2026, 'triwulan' => 'all']));
        $resAll->assertStatus(200);
        $rowsAll = $resAll->viewData('rmbRows');
        $this->assertEquals(80000000, $rowsAll['1.3.1.01']['c1_belanja_modal']);
    }
}
