<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Astap;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecycleBinBelanjaModalTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.sqlite.database' => database_path('database.sqlite')]);
        \DB::purge('sqlite');
    }

    public function test_recycle_bin_page_loads_with_belanja_modal_data(): void
    {
        $user = User::first();

        $response = $this->actingAs($user)->get('/recycle-bin');

        $response->assertStatus(200);
        $response->assertSee('Belanja Modal');
        $response->assertSee('belanja_modal');
        $response->assertSee('Paket Pengadaan Belanja Modal');
        $response->assertSee('Unit Fisik NIBAR');
        $response->assertViewHas('deletedBelanjaModals');
        $response->assertViewHas('deletedBelanjaModalNibars');
    }

    public function test_restore_and_force_delete_belanja_modal(): void
    {
        \DB::beginTransaction();
        try {
            $user = User::first();

            // Buat paket belanja modal terhapus dummy
            $astap = Astap::create([
                'nama_barang' => 'Test Belanja Modal Ventilator Recycle Bin',
                'sumber_dana' => 'belanja_modal',
                'jumlah_volume' => 1,
                'satuan' => 'Unit',
                'harga_satuan' => 50000000,
                'total_realisasi' => 50000000,
                'tahun_perolehan' => 2026,
                'is_deleted' => 1,
                'deleted_at' => now(),
                'deleted_by' => 'Tester',
            ]);

            // Test Restore Endpoint
            $restoreRes = $this->actingAs($user)
                ->withHeaders(['Accept' => 'application/json'])
                ->postJson("/recycle-bin/belanja_modal/{$astap->id}/restore");

            $restoreRes->assertStatus(200);
            $restoreRes->assertJsonFragment(['success' => true]);

            // Verifikasi status aktif
            $astap->refresh();
            $this->assertEquals(0, $astap->is_deleted);
            $this->assertNull($astap->deleted_at);

            // Re-delete untuk test force-delete
            $astap->update(['is_deleted' => 1, 'deleted_at' => now()]);

            $deleteRes = $this->actingAs($user)
                ->withHeaders(['Accept' => 'application/json'])
                ->deleteJson("/recycle-bin/belanja_modal/{$astap->id}/force-delete");

            $deleteRes->assertStatus(200);
            $deleteRes->assertJsonFragment(['success' => true]);

            $this->assertNull(Astap::find($astap->id));
        } finally {
            \DB::rollBack();
        }
    }
}
