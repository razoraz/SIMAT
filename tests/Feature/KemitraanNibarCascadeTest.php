<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Astap;
use App\Models\AstapKemitraan;
use App\Models\AstapRegister;
use App\Models\JenisAstap;

class KemitraanNibarCascadeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.sqlite.database' => database_path('database.sqlite')]);
        \DB::purge('sqlite');
    }

    public function test_deleting_single_nibar_reduces_volume_while_pks_remains_active(): void
    {
        $admin = User::whereIn('role', ['admin', 'master_admin'])->first() ?: User::first();
        $this->actingAs($admin);

        $jenisAstap = JenisAstap::first();
        $randSuffix = rand(10000, 99999);

        // 1. Buat paket kemitraan dengan 2 unit register NIBAR
        $astap = Astap::create([
            'nama_barang'      => 'Aset KSO Multi Unit ' . $randSuffix,
            'jenis_astap_id'   => $jenisAstap->id,
            'tahun_perolehan'  => 2026,
            'jumlah_volume'    => 2,
            'satuan'           => 'Unit',
            'harga_satuan'     => 10000000,
            'total_realisasi'  => 20000000,
            'sumber_dana'      => 'kemitraan',
            'is_deleted'       => 0,
        ]);

        $reg1 = AstapRegister::create([
            'astap_id'        => $astap->id,
            'tahun_perolehan' => 2026,
            'no_register_int' => $randSuffix . '1',
            'no_register'     => 'NIBAR-MULTI-' . $randSuffix . '-1',
            'nibar'           => 'NIBAR-MULTI-' . $randSuffix . '-1',
            'status'          => 'Kemitraan',
            'kondisi'         => 'Baik',
            'is_deleted'      => 0,
        ]);

        $reg2 = AstapRegister::create([
            'astap_id'        => $astap->id,
            'tahun_perolehan' => 2026,
            'no_register_int' => $randSuffix . '2',
            'no_register'     => 'NIBAR-MULTI-' . $randSuffix . '-2',
            'nibar'           => 'NIBAR-MULTI-' . $randSuffix . '-2',
            'status'          => 'Kemitraan',
            'kondisi'         => 'Baik',
            'is_deleted'      => 0,
        ]);

        $kemitraan = AstapKemitraan::create([
            'astap_id'        => $astap->id,
            'mitra_nama'      => 'PT. Multi Unit Indo',
            'nomor_pks'       => 'PKS-MULTI-' . $randSuffix,
            'tanggal_pks'     => now()->toDateString(),
            'status_konsesi'  => 'Aktif',
            'jumlah_volume'   => 2,
            'satuan'          => 'Unit',
            'nilai_aset'      => 20000000,
            'tipe_kemitraan'  => 'ditambahkan',
            'is_deleted'      => 0,
        ]);

        try {
            // Hapus unit register pertama (sisa 1 unit)
            $res = $this->deleteJson('/astap-register/' . $reg1->id);
            $res->assertOk();
            $res->assertJson(['success' => true, 'astap_auto_deleted' => false]);

            // Register 1 terhapus, Register 2 masih aktif
            $this->assertEquals(1, $reg1->fresh()->is_deleted);
            $this->assertEquals(0, $reg2->fresh()->is_deleted);

            // ASTAP & PKS Kemitraan tetap aktif dengan volume disinkronkan menjadi 1
            $this->assertEquals(0, $astap->fresh()->is_deleted);
            $this->assertEquals(1, $astap->fresh()->jumlah_volume);
            $this->assertEquals(0, $kemitraan->fresh()->is_deleted);
            $this->assertEquals(1, $kemitraan->fresh()->jumlah_volume);

            // Cek di halaman Kemitraan: PKS masih muncul
            $kemitraanPage = $this->get(route('master.kemitraan'));
            $kemitraanPage->assertOk();
            $kemitraanPage->assertSee('PKS-MULTI-' . $randSuffix);
        } finally {
            $reg1->forceDelete();
            $reg2->forceDelete();
            $kemitraan->forceDelete();
            $astap->forceDelete();
        }
    }

    public function test_deleting_last_nibar_auto_deletes_entire_pks_and_astap_to_recycle_bin(): void
    {
        $admin = User::whereIn('role', ['admin', 'master_admin'])->first() ?: User::first();
        $this->actingAs($admin);

        $jenisAstap = JenisAstap::first();
        $randSuffix = rand(10000, 99999);

        // 1. Buat paket kemitraan dengan 1 unit register NIBAR
        $astap = Astap::create([
            'nama_barang'      => 'Aset KSO Single Unit ' . $randSuffix,
            'jenis_astap_id'   => $jenisAstap->id,
            'tahun_perolehan'  => 2026,
            'jumlah_volume'    => 1,
            'satuan'           => 'Unit',
            'harga_satuan'     => 15000000,
            'total_realisasi'  => 15000000,
            'sumber_dana'      => 'kemitraan',
            'is_deleted'       => 0,
        ]);

        $reg = AstapRegister::create([
            'astap_id'        => $astap->id,
            'tahun_perolehan' => 2026,
            'no_register_int' => $randSuffix,
            'no_register'     => 'NIBAR-SINGLE-' . $randSuffix,
            'nibar'           => 'NIBAR-SINGLE-' . $randSuffix,
            'status'          => 'Kemitraan',
            'kondisi'         => 'Baik',
            'is_deleted'      => 0,
        ]);

        $kemitraan = AstapKemitraan::create([
            'astap_id'        => $astap->id,
            'mitra_nama'      => 'PT. Single Unit Indo',
            'nomor_pks'       => 'PKS-SINGLE-' . $randSuffix,
            'tanggal_pks'     => now()->toDateString(),
            'status_konsesi'  => 'Aktif',
            'jumlah_volume'   => 1,
            'satuan'          => 'Unit',
            'nilai_aset'      => 15000000,
            'tipe_kemitraan'  => 'ditambahkan',
            'is_deleted'      => 0,
        ]);

        try {
            // Hapus satu-satunya unit register NIBAR
            $res = $this->deleteJson('/astap-register/' . $reg->id);
            $res->assertOk();
            $res->assertJson(['success' => true, 'astap_auto_deleted' => true]);

            // Register ter-soft delete
            $this->assertEquals(1, $reg->fresh()->is_deleted);

            // Seluruh paket ASTAP dan PKS Kemitraan OTOMATIS ikut ter-soft delete ke Recycle Bin
            $this->assertEquals(1, $astap->fresh()->is_deleted, 'Master ASTAP harus otomatis ter-soft delete saat NIBAR habis');
            $this->assertEquals(1, $kemitraan->fresh()->is_deleted, 'PKS Kemitraan harus otomatis ter-soft delete saat NIBAR habis');

            // Cek di halaman Kemitraan: PKS TIDAK BOLEH MUNCUL LAGI (tidak ada 0 / 0 Aset)
            $kemitraanPage = $this->get(route('master.kemitraan'));
            $kemitraanPage->assertOk();
            $kemitraanPage->assertDontSee('PKS-SINGLE-' . $randSuffix);

            // Cek di halaman Master ASTAP: ASTAP tidak boleh ada di koleksi astaps aktif
            $astapPage = $this->get(route('astap.index'));
            $astapPage->assertOk();
            $this->assertFalse(
                collect($astapPage->viewData('astaps'))->contains('id', $astap->id),
                'ASTAP terhapus tidak boleh masuk di daftar koleksi astaps aktif'
            );

            // Cek di Recycle Bin: PKS harus muncul di tab Kemitraan (Dokumen Kontrak / PKS)
            $recyclePage = $this->get(route('recycle_bin.index', ['tab' => 'kemitraan']));
            $recyclePage->assertOk();
            $recyclePage->assertSee('PKS-SINGLE-' . $randSuffix);

            // 4. Test Restore dari Recycle Bin (modul kemitraan)
            $restoreRes = $this->postJson(route('recycle_bin.restore', ['module' => 'kemitraan', 'id' => $kemitraan->id]));
            $restoreRes->assertOk();

            // PKS, ASTAP, dan Register NIBAR harus pulih kembali bersamaan
            $this->assertEquals(0, $kemitraan->fresh()->is_deleted, 'PKS Kemitraan harus pulih');
            $this->assertEquals(0, $astap->fresh()->is_deleted, 'Master ASTAP harus pulih');
            $this->assertEquals(0, $reg->fresh()->is_deleted, 'Register NIBAR harus pulih bersama paket PKS');
            $this->assertEquals(1, $astap->fresh()->jumlah_volume);
            $this->assertEquals(1, $kemitraan->fresh()->jumlah_volume);
        } finally {
            $reg->forceDelete();
            $kemitraan->forceDelete();
            $astap->forceDelete();
        }
    }

    public function test_bulk_restore_kemitraan_restores_all_nibar_and_syncs_volume(): void
    {
        $admin = User::whereIn('role', ['admin', 'master_admin'])->first() ?: User::first();
        $this->actingAs($admin);

        $jenisAstap = JenisAstap::first();
        $randSuffix = rand(10000, 99999);

        // Buat paket kemitraan
        $astap = Astap::create([
            'nama_barang'      => 'Aset KSO Bulk ' . $randSuffix,
            'jenis_astap_id'   => $jenisAstap->id,
            'tahun_perolehan'  => 2026,
            'jumlah_volume'    => 1,
            'satuan'           => 'Unit',
            'harga_satuan'     => 12000000,
            'total_realisasi'  => 12000000,
            'sumber_dana'      => 'kemitraan',
            'is_deleted'       => 0,
        ]);

        $reg = AstapRegister::create([
            'astap_id'        => $astap->id,
            'tahun_perolehan' => 2026,
            'no_register_int' => $randSuffix,
            'no_register'     => 'NIBAR-BULK-' . $randSuffix,
            'nibar'           => 'NIBAR-BULK-' . $randSuffix,
            'status'          => 'Kemitraan',
            'kondisi'         => 'Baik',
            'is_deleted'      => 0,
        ]);

        $kemitraan = AstapKemitraan::create([
            'astap_id'        => $astap->id,
            'mitra_nama'      => 'PT. Bulk Unit Indo',
            'nomor_pks'       => 'PKS-BULK-' . $randSuffix,
            'tanggal_pks'     => now()->toDateString(),
            'status_konsesi'  => 'Aktif',
            'jumlah_volume'   => 1,
            'satuan'          => 'Unit',
            'nilai_aset'      => 12000000,
            'tipe_kemitraan'  => 'ditambahkan',
            'is_deleted'      => 0,
        ]);

        try {
            // Hapus NIBAR terakhir -> auto hapus PKS
            $res = $this->deleteJson('/astap-register/' . $reg->id);
            $res->assertOk();
            $res->assertJson(['success' => true, 'astap_auto_deleted' => true]);

            $this->assertEquals(1, $kemitraan->fresh()->is_deleted);
            $this->assertEquals(1, $astap->fresh()->is_deleted);
            $this->assertEquals(1, $reg->fresh()->is_deleted);

            // Test bulk restore
            $bulkRes = $this->postJson(route('recycle_bin.bulk_restore', ['module' => 'kemitraan']), [
                'module' => 'kemitraan',
                'ids'    => [$kemitraan->id],
            ]);
            $bulkRes->assertOk();

            $this->assertEquals(0, $kemitraan->fresh()->is_deleted, 'PKS harus pulih setelah bulk restore');
            $this->assertEquals(0, $astap->fresh()->is_deleted, 'ASTAP harus pulih setelah bulk restore');
            $this->assertEquals(0, $reg->fresh()->is_deleted, 'Register NIBAR harus pulih setelah bulk restore');
            $this->assertEquals(1, $astap->fresh()->jumlah_volume);
            $this->assertEquals(1, $kemitraan->fresh()->jumlah_volume);
        } finally {
            $reg->forceDelete();
            $kemitraan->forceDelete();
            $astap->forceDelete();
        }
    }
}

