<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Astap;
use App\Models\AstapKemitraan;
use App\Models\AstapRegister;
use App\Models\JenisAstap;
use Carbon\Carbon;

class KemitraanBugFixesTest extends TestCase
{
    use \Illuminate\Foundation\Testing\DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_sync_expired_status_expires_both_aktif_and_perpanjangan(): void
    {
        $jenisAstap = JenisAstap::first();
        $suffix = rand(10000, 99999);

        $astap = Astap::create([
            'nama_barang'     => 'Aset Test Expired ' . $suffix,
            'jenis_astap_id'  => $jenisAstap->id,
            'tahun_perolehan' => 2026,
            'jumlah_volume'   => 1,
            'satuan'          => 'Unit',
            'harga_satuan'    => 1000000,
            'total_realisasi' => 1000000,
            'sumber_dana'     => 'kemitraan',
            'is_deleted'      => 0,
        ]);

        $kemAktif = AstapKemitraan::create([
            'astap_id'        => $astap->id,
            'mitra_nama'      => 'PT Test Mitra A',
            'nomor_pks'       => 'PKS-EXP-A-' . $suffix,
            'tanggal_pks'     => Carbon::yesterday(),
            'tanggal_mulai'   => Carbon::yesterday()->subMonth(),
            'tanggal_selesai' => Carbon::yesterday(),
            'status_konsesi'  => 'Aktif',
            'is_deleted'      => 0,
        ]);

        $kemPerpanjangan = AstapKemitraan::create([
            'astap_id'        => $astap->id,
            'mitra_nama'      => 'PT Test Mitra B',
            'nomor_pks'       => 'PKS-EXP-B-' . $suffix,
            'tanggal_pks'     => Carbon::yesterday(),
            'tanggal_mulai'   => Carbon::yesterday()->subMonth(),
            'tanggal_selesai' => Carbon::yesterday(),
            'status_konsesi'  => 'Perpanjangan',
            'is_deleted'      => 0,
        ]);

        $count = AstapKemitraan::syncExpiredStatus();
        $this->assertGreaterThanOrEqual(2, $count);

        $this->assertEquals('Konsesi Berakhir', $kemAktif->fresh()->status_konsesi);
        $this->assertEquals('Konsesi Berakhir', $kemPerpanjangan->fresh()->status_konsesi);
    }

    public function test_batal_pembatalan_cascades_to_restore_child_assets(): void
    {
        $admin = User::whereIn('role', ['admin', 'master_admin'])->first() ?: User::first();
        $jenisAstap = JenisAstap::first();
        $suffix = rand(10000, 99999);

        // Parent (Objek Dimanfaatkan)
        $parentAstap = Astap::create([
            'nama_barang'     => 'Ruang Induk Dimanfaatkan ' . $suffix,
            'jenis_astap_id'  => $jenisAstap->id,
            'tahun_perolehan' => 2026,
            'jumlah_volume'   => 1,
            'satuan'          => 'Unit',
            'harga_satuan'    => 50000000,
            'total_realisasi' => 50000000,
            'sumber_dana'     => 'kemitraan',
            'is_deleted'      => 0,
        ]);

        $parentKemitraan = AstapKemitraan::create([
            'astap_id'        => $parentAstap->id,
            'mitra_nama'      => 'PT Mitra Lab',
            'nomor_pks'       => 'PKS-BATAL-' . $suffix,
            'tanggal_pks'     => now(),
            'tanggal_mulai'   => now(),
            'tanggal_selesai' => now()->addYear(),
            'tipe_kemitraan'  => 'dimanfaatkan',
            'status_konsesi'  => 'Aktif',
            'is_deleted'      => 0,
        ]);

        // Child (Aset Ditambahkan Mitra)
        $childAstap = Astap::create([
            'nama_barang'     => 'Alat Laboratorium ' . $suffix,
            'jenis_astap_id'  => $jenisAstap->id,
            'tahun_perolehan' => 2026,
            'jumlah_volume'   => 1,
            'satuan'          => 'Unit',
            'harga_satuan'    => 20000000,
            'total_realisasi' => 20000000,
            'sumber_dana'     => 'kemitraan',
            'is_deleted'      => 0,
        ]);

        $childKemitraan = AstapKemitraan::create([
            'astap_id'        => $childAstap->id,
            'objek_astap_id'  => $parentAstap->id,
            'mitra_nama'      => 'PT Mitra Lab',
            'nomor_pks'       => 'PKS-BATAL-' . $suffix,
            'tanggal_pks'     => now(),
            'tanggal_mulai'   => now(),
            'tanggal_selesai' => now()->addYear(),
            'tipe_kemitraan'  => 'ditambahkan',
            'status_konsesi'  => 'Aktif',
            'is_deleted'      => 0,
        ]);

        // 1. Batalkan kemitraan induk
        $resBatal = $this->actingAs($admin)->postJson(route('master.kemitraan.batalkan', $parentKemitraan->id), [
            'alasan_batal' => 'Uji coba pembatalan kontrak',
        ]);
        $resBatal->assertStatus(200);
        $this->assertEquals('Dibatalkan', $parentKemitraan->fresh()->status_konsesi);
        $this->assertEquals('Dibatalkan', $childKemitraan->fresh()->status_konsesi);

        // 2. Cabut pembatalan (Batal Pembatalan)
        $resUndo = $this->actingAs($admin)->postJson(route('master.kemitraan.batal_pembatalan', $parentKemitraan->id));
        $resUndo->assertStatus(200);

        // Verifikasi induk pulih
        $this->assertEquals('Aktif', $parentKemitraan->fresh()->status_konsesi);
        // Verifikasi anak ikut pulih menjadi Aktif!
        $this->assertEquals('Aktif', $childKemitraan->fresh()->status_konsesi);
        $this->assertNull($childKemitraan->fresh()->alasan_batal);
    }

    public function test_update_metadata_cascades_to_linked_partner_assets(): void
    {
        $admin = User::whereIn('role', ['admin', 'master_admin'])->first() ?: User::first();
        $jenisAstap = JenisAstap::first();
        $suffix = rand(10000, 99999);

        // Parent
        $parentAstap = Astap::create([
            'nama_barang'     => 'Gedung Poli ' . $suffix,
            'jenis_astap_id'  => $jenisAstap->id,
            'tahun_perolehan' => 2026,
            'jumlah_volume'   => 1,
            'satuan'          => 'Unit',
            'harga_satuan'    => 100000000,
            'total_realisasi' => 100000000,
            'sumber_dana'     => 'kemitraan',
            'is_deleted'      => 0,
        ]);

        $parentKemitraan = AstapKemitraan::create([
            'astap_id'        => $parentAstap->id,
            'mitra_nama'      => 'Mitra Lama',
            'nomor_pks'       => 'PKS-OLD-' . $suffix,
            'tanggal_pks'     => '2026-01-01',
            'tipe_kemitraan'  => 'dimanfaatkan',
            'status_konsesi'  => 'Aktif',
            'is_deleted'      => 0,
        ]);

        // Child
        $childAstap = Astap::create([
            'nama_barang'     => 'Mesin Rontgen ' . $suffix,
            'jenis_astap_id'  => $jenisAstap->id,
            'tahun_perolehan' => 2026,
            'jumlah_volume'   => 1,
            'satuan'          => 'Unit',
            'harga_satuan'    => 50000000,
            'total_realisasi' => 50000000,
            'sumber_dana'     => 'kemitraan',
            'is_deleted'      => 0,
        ]);

        $childKemitraan = AstapKemitraan::create([
            'astap_id'        => $childAstap->id,
            'objek_astap_id'  => $parentAstap->id,
            'mitra_nama'      => 'Mitra Lama',
            'nomor_pks'       => 'PKS-OLD-' . $suffix,
            'tanggal_pks'     => '2026-01-01',
            'tipe_kemitraan'  => 'ditambahkan',
            'status_konsesi'  => 'Aktif',
            'is_deleted'      => 0,
        ]);

        // Panggil endpoint patch update
        $resUpdate = $this->actingAs($admin)->patchJson(route('master.kemitraan.update', $parentKemitraan->id), [
            'nomor_pks'  => 'PKS-NEW-CASCADE-' . $suffix,
            'mitra_nama' => 'PT Mitra Terpadu Baru',
        ]);
        $resUpdate->assertStatus(200);

        // Periksa anak ikut ter-update
        $this->assertEquals('PKS-NEW-CASCADE-' . $suffix, $childKemitraan->fresh()->nomor_pks);
        $this->assertEquals('PT Mitra Terpadu Baru', $childKemitraan->fresh()->mitra_nama);
    }
}
