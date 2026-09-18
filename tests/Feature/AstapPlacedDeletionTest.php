<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Unit;
use App\Models\Astap;
use App\Models\AstapRegister;
use Tests\TestCase;

class AstapPlacedDeletionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.sqlite.database' => database_path('database.sqlite')]);
        \DB::purge('sqlite');
    }

    /**
     * Uji pemblokiran hapus master ASTAP jika ada register yang ditempatkan di unit/paviliun.
     */
    public function test_cannot_delete_astap_when_registers_are_placed_in_unit(): void
    {
        $admin = User::where('role', 'master_admin')->first();
        if (!$admin) {
            $admin = User::factory()->create(['role' => 'master_admin']);
        }

        // Cari atau buat master ASTAP
        $astap = Astap::first();
        $this->assertNotNull($astap);

        // Buat register yang ditempatkan di ruangan unit
        $placedReg = AstapRegister::create([
            'astap_id'        => $astap->id,
            'tahun_perolehan' => 2026,
            'no_register_int' => 99998,
            'no_register'     => 'TEST-PLACED-' . time(),
            'nibar'           => 'TEST-PLACED-' . time(),
            'ruang_pemegang'  => 'Paviliun Anak & Perinatologi',
            'kondisi'         => 'Baik',
            'status'          => 'Tidak Tersedia',
            'is_deleted'      => 0,
        ]);

        $response = $this->actingAs($admin)->deleteJson('/astap/' . $astap->id);

        $response->assertStatus(422);
        $response->assertJson([
            'success'    => false,
            'is_blocked' => true,
            'action_url' => '/mutasi-aset',
        ]);

        // Bersihkan data uji
        $placedReg->forceDelete();
    }

    /**
     * Uji pemblokiran hapus single register jika sudah ditempatkan di ruangan unit/paviliun.
     */
    public function test_cannot_delete_single_register_when_placed_in_unit(): void
    {
        $admin = User::where('role', 'master_admin')->first();
        if (!$admin) {
            $admin = User::factory()->create(['role' => 'master_admin']);
        }

        $astap = Astap::first();
        $this->assertNotNull($astap);

        $placedReg = AstapRegister::create([
            'astap_id'        => $astap->id,
            'tahun_perolehan' => 2026,
            'no_register_int' => 99999,
            'no_register'     => 'TEST-REG-PLACED-' . time(),
            'nibar'           => 'TEST-REG-PLACED-' . time(),
            'ruang_pemegang'  => 'Instalasi Radiologi & Imaging Sentral',
            'kondisi'         => 'Baik',
            'status'          => 'Tidak Tersedia',
            'is_deleted'      => 0,
        ]);

        $response = $this->actingAs($admin)->deleteJson('/astap-register/' . $placedReg->id);

        $response->assertStatus(422);
        $response->assertJson([
            'success'    => false,
            'is_blocked' => true,
            'action_url' => '/mutasi-aset',
        ]);

        // Bersihkan data uji
        $placedReg->forceDelete();
    }

    /**
     * Uji penghapusan register yang BELUM ditempatkan (di gudang aset / null) diizinkan soft delete.
     */
    public function test_can_delete_single_register_when_unplaced(): void
    {
        $admin = User::where('role', 'master_admin')->first();
        if (!$admin) {
            $admin = User::factory()->create(['role' => 'master_admin']);
        }

        $astap = Astap::first();
        $this->assertNotNull($astap);

        $unplacedReg = AstapRegister::create([
            'astap_id'        => $astap->id,
            'tahun_perolehan' => 2026,
            'no_register_int' => 99997,
            'no_register'     => 'TEST-REG-UNPLACED-' . time(),
            'nibar'           => 'TEST-REG-UNPLACED-' . time(),
            'ruang_pemegang'  => 'Belum Ditempatkan',
            'kondisi'         => 'Baik',
            'status'          => 'Tersedia',
            'is_deleted'      => 0,
        ]);

        $response = $this->actingAs($admin)->deleteJson('/astap-register/' . $unplacedReg->id);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertEquals(1, $unplacedReg->fresh()->is_deleted);

        // Bersihkan data uji
        $unplacedReg->forceDelete();
    }
}
