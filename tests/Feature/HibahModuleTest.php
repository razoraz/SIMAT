<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Astap;
use App\Models\AstapHibah;
use App\Models\AstapRegister;
use App\Models\JenisAstap;
use App\Models\Unit;
use App\Http\Controllers\RecycleBinController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

class HibahModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected JenisAstap $jenisAstap;
    protected ?Unit $unit;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'master_admin']);
        $this->jenisAstap = JenisAstap::create([
            'jenis'                  => '1.3.2',
            'nama_jenis'             => 'Peralatan dan Mesin',
            'sub_rincian_objek'      => '1.3.2.05',
            'uraian_sub_rincian'     => 'Alat Kedokteran',
            'sub_sub_rincian_objek'  => '1.3.2.05.01.01.001',
            'uraian_sub_sub_rincian' => 'Mesin Uji Coba Hibah',
        ]);
        $this->unit = Unit::first();
    }

    public function test_hibah_store_and_update_lifecycle(): void
    {
        $this->actingAs($this->user);

        $suffix = rand(10000, 99999);
        $bastNum = 'BAST-TEST-' . $suffix;

        // 1. Store new Hibah asset (volume 2)
        $storePayload = [
            'nama_barang'        => 'USG Diagnostic Test ' . $suffix,
            'jenis_astap_id'     => $this->jenisAstap->id,
            'tahun_perolehan'    => 2026,
            'jumlah_volume'      => 2,
            'satuan'             => 'Unit',
            'total_realisasi'    => 20000000,
            'triwulan'           => 'TW I',
            'hibah_pemberi'      => 'Kemenkes RI',
            'hibah_nomor_bast'   => $bastNum,
            'hibah_tanggal_bast' => '2026-03-15',
            'hibah_keterangan'   => 'Hibah alat medis untuk radiologi',
            'unit_id'            => $this->unit?->id,
            'alamat_barang'      => 'Gedung Teratai Lt. 2',
            'kondisi'            => 'Baik',
        ];

        $response = $this->postJson(route('astap.store_hibah'), $storePayload);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify Astap and registers created
        $astap = Astap::where('nama_barang', 'USG Diagnostic Test ' . $suffix)->first();
        $this->assertNotNull($astap);
        $this->assertEquals(2, $astap->jumlah_volume);
        $this->assertEquals('hibah', $astap->sumber_dana);

        $hibah = AstapHibah::where('astap_id', $astap->id)->first();
        $this->assertNotNull($hibah);
        $this->assertEquals($bastNum, $hibah->nomor_bast);
        $this->assertEquals('masuk', $hibah->tipe_hibah);

        $registers = AstapRegister::where('astap_id', $astap->id)->get();
        $this->assertCount(2, $registers);
        $this->assertEquals(0, $registers[0]->is_deleted);
        $this->assertNotEmpty($registers[0]->nibar);

        // 2. Update Hibah asset (increase volume to 3 and update details)
        $updatePayload = [
            'nama_barang'        => 'USG Diagnostic Test ' . $suffix . ' (Upgraded)',
            'tahun_perolehan'    => 2026,
            'jumlah_volume'      => 3,
            'satuan'             => 'Unit',
            'total_realisasi'    => 30000000,
            'triwulan'           => 'TW II',
            'hibah_pemberi'      => 'Kemenkes RI Pusat',
            'hibah_nomor_bast'   => $bastNum . '-REV',
            'hibah_tanggal_bast' => '2026-04-10',
            'hibah_keterangan'   => 'Hibah alat medis telah diperbarui',
            'unit_id'            => $this->unit?->id,
            'alamat_barang'      => 'Gedung Teratai Lt. 3',
        ];

        $updateResponse = $this->postJson(route('astap.update_hibah', $astap->id), $updatePayload);
        $updateResponse->assertStatus(200);
        $updateResponse->assertJson(['success' => true]);

        $astap->refresh();
        $this->assertEquals('USG Diagnostic Test ' . $suffix . ' (Upgraded)', $astap->nama_barang);
        $this->assertEquals(3, $astap->jumlah_volume);
        $this->assertEquals(30000000, (float)$astap->total_realisasi);

        $hibah->refresh();
        $this->assertEquals($bastNum . '-REV', $hibah->nomor_bast);
        $this->assertEquals('Kemenkes RI Pusat', $hibah->pihak_hibah);

        $registersUpdated = AstapRegister::where('astap_id', $astap->id)->get();
        $this->assertCount(3, $registersUpdated);
        $this->assertNotEmpty($registersUpdated[2]->nibar);

        // 3. Test Delete (Destroy) Cascading
        $deleteResponse = $this->deleteJson(route('master.hibah.destroy', $hibah->id));
        $deleteResponse->assertStatus(200);

        $hibah->refresh();
        $astap->refresh();
        $this->assertEquals(1, $hibah->is_deleted);
        $this->assertEquals(1, $astap->is_deleted);

        $deletedRegisters = AstapRegister::where('astap_id', $astap->id)->where('is_deleted', 1)->count();
        $this->assertEquals(3, $deletedRegisters);

        // 4. Test RecycleBin Restore Cascading
        $recycleController = new RecycleBinController();
        $restoreRequest = new Request();
        $recycleController->restore($restoreRequest, 'hibah', $hibah->id);

        $hibah->refresh();
        $astap->refresh();
        $this->assertEquals(0, $hibah->is_deleted);
        $this->assertEquals(0, $astap->is_deleted);
        $activeRegisters = AstapRegister::where('astap_id', $astap->id)->where('is_deleted', 0)->count();
        $this->assertEquals(3, $activeRegisters);

        // 5. Soft-delete again, then test Force Delete Permanently
        $this->deleteJson(route('master.hibah.destroy', $hibah->id));
        $forceRequest = new Request();
        $recycleController->forceDelete($forceRequest, 'hibah', $hibah->id);

        $this->assertNull(AstapHibah::find($hibah->id));
        $this->assertNull(Astap::find($astap->id));
        $this->assertEquals(0, AstapRegister::where('astap_id', $astap->id)->count());
    }

    public function test_hibah_kib_b_extracom_and_kib_c_gedung_status_fisik(): void
    {
        $this->actingAs($this->user);

        // 1. Test KIB B Peralatan Mesin dengan Extrakomtabel & Kondisi
        $mesinPayload = [
            'nama_barang'        => 'Kursi Roda Pasien Hibah',
            'jenis_astap_id'     => $this->jenisAstap->id,
            'tahun_perolehan'    => 2026,
            'jumlah_volume'      => 1,
            'satuan'             => 'Unit',
            'total_realisasi'    => 250000,
            'triwulan'           => 'TW I',
            'hibah_pemberi'      => 'CSR PT Bank Jatim',
            'hibah_nomor_bast'   => 'BAST-MESIN-001',
            'hibah_tanggal_bast' => '2026-02-10',
            'is_extracomtable'   => true,
            'spesifikasi_json'   => json_encode([
                'mesin_items' => [
                    [
                        'mesin_nama_barang' => 'Kursi Roda Pasien Hibah',
                        'mesin_merk'        => 'Standard',
                        'mesin_type'        => 'Manual',
                        'mesin_ukuran'      => 'Sedang',
                        'mesin_bahan'       => 'Stainless Steel',
                        'mesin_no_pabrik'   => 'SN-EXT-9988',
                        'mesin_kondisi'     => 'Baik',
                        'is_extracom'       => true,
                    ]
                ]
            ])
        ];

        $respB = $this->postJson(route('astap.store_hibah'), $mesinPayload);
        $respB->assertStatus(200);

        $astapB = Astap::where('nama_barang', 'Kursi Roda Pasien Hibah')->first();
        $this->assertNotNull($astapB);
        $this->assertEquals(1, $astapB->is_extracomtable);
        $this->assertEquals('Baik', $astapB->spesifikasi_json['kondisi']);
        $this->assertEquals('Standard', $astapB->spesifikasi_json['merk']);
        $this->assertEquals('SN-EXT-9988', $astapB->spesifikasi_json['no_pabrik']);

        // 2. Test KIB C Gedung & Bangunan dengan Status Fisik Bangunan (gedung_is_baru)
        $jenisGedung = JenisAstap::create([
            'jenis'                  => '1.3.3',
            'nama_jenis'             => 'Gedung dan Bangunan',
            'sub_rincian_objek'      => '1.3.3.01',
            'uraian_sub_rincian'     => 'Bangunan Gedung',
            'sub_sub_rincian_objek'  => '1.3.3.01.01.01.001',
            'uraian_sub_sub_rincian' => 'Gedung Hibah Paviliun',
        ]);

        $gedungPayload = [
            'nama_barang'        => 'Gedung Paviliun Rawat Inap Hibah',
            'jenis_astap_id'     => $jenisGedung->id,
            'tahun_perolehan'    => 2026,
            'jumlah_volume'      => 1,
            'satuan'             => 'Gedung',
            'total_realisasi'    => 500000000,
            'triwulan'           => 'TW I',
            'hibah_pemberi'      => 'Yayasan Kesehatan Mandiri',
            'hibah_nomor_bast'   => 'BAST-GDG-8877',
            'hibah_tanggal_bast' => '2026-03-01',
            'spesifikasi_json'   => json_encode([
                'gedung_items' => [
                    [
                        'gedung_nama_barang' => 'Gedung Paviliun Rawat Inap Hibah',
                        'gedung_luas_m2'     => 450,
                        'gedung_kondisi'     => 'Baik',
                        'gedung_bertingkat'  => 'Bertingkat',
                        'gedung_beton'       => 'Beton',
                        'gedung_is_baru'     => 'Lama',
                    ]
                ]
            ])
        ];

        $respC = $this->postJson(route('astap.store_hibah'), $gedungPayload);
        $respC->assertStatus(200);

        $astapC = Astap::where('nama_barang', 'Gedung Paviliun Rawat Inap Hibah')->first();
        $this->assertNotNull($astapC);
        $this->assertEquals(450, $astapC->spesifikasi_json['gedung_luas_m2']);
        $this->assertEquals('Bertingkat', $astapC->spesifikasi_json['gedung_bertingkat']);
        $this->assertEquals('Lama', $astapC->spesifikasi_json['gedung_is_baru']);

        // Update KIB C menjadi status 'Baru'
        $updateGedungPayload = [
            'nama_barang'        => 'Gedung Paviliun Rawat Inap Hibah (Revisi)',
            'tahun_perolehan'    => 2026,
            'jumlah_volume'      => 1,
            'satuan'             => 'Gedung',
            'total_realisasi'    => 550000000,
            'triwulan'           => 'TW I',
            'hibah_pemberi'      => 'Yayasan Kesehatan Mandiri',
            'hibah_nomor_bast'   => 'BAST-GDG-8877-REV',
            'hibah_tanggal_bast' => '2026-03-05',
            'spesifikasi_json'   => json_encode([
                'gedung_items' => [
                    [
                        'gedung_nama_barang' => 'Gedung Paviliun Rawat Inap Hibah (Revisi)',
                        'gedung_luas_m2'     => 500,
                        'gedung_kondisi'     => 'Baik',
                        'gedung_bertingkat'  => 'Bertingkat',
                        'gedung_beton'       => 'Beton',
                        'gedung_is_baru'     => 'Baru',
                    ]
                ]
            ])
        ];

        $updateResp = $this->postJson(route('astap.update_hibah', $astapC->id), $updateGedungPayload);
        $updateResp->assertStatus(200);

        $astapC->refresh();
        $this->assertEquals('Baru', $astapC->spesifikasi_json['gedung_is_baru']);
        $this->assertEquals(500, $astapC->spesifikasi_json['gedung_luas_m2']);
    }
}
