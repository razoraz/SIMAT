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
}
