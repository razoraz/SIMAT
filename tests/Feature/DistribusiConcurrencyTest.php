<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Unit;
use App\Models\Astap;
use App\Models\AstapRegister;
use App\Models\Distribusi;
use App\Models\DistribusiItem;
use App\Models\DistribusiItemRegister;
use App\Http\Controllers\DistribusiController;
use Tests\TestCase;

class DistribusiConcurrencyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.sqlite.database' => database_path('database.sqlite')]);
        \DB::purge('sqlite');
    }

    /**
     * Kasus 1: NIBAR yang sedang dalam status 'Dalam Pengiriman' (belum di-oke oleh Sub Admin 1)
     * TIDAK BOLEH muncul di dropdown form input atau edit distribusi lain.
     */
    public function test_nibar_in_transit_is_excluded_from_available_registers(): void
    {
        $admin = User::whereIn('role', ['admin', 'master_admin'])->first() ?? User::factory()->create(['role' => 'master_admin']);
        $unit1 = Unit::first() ?? Unit::create(['nama' => 'Unit Test 1', 'kepala' => 'PJ 1']);
        $unit2 = Unit::skip(1)->first() ?? Unit::create(['nama' => 'Unit Test 2', 'kepala' => 'PJ 2']);

        $astap = Astap::first();
        $this->assertNotNull($astap, 'Master ASTAP must exist');

        // Buat register uji baru yang tersedia
        $testNibar = 'TESTNIBAR-' . time() . '-001';
        $register = AstapRegister::create([
            'astap_id'        => $astap->id,
            'tahun_perolehan' => 2026,
            'no_register_int' => 9991,
            'no_register'     => $testNibar,
            'nibar'           => $testNibar,
            'ruang_pemegang'  => null,
            'status'          => 'Tersedia',
            'kondisi'         => 'Baik',
        ]);

        // Sebelum didistribusikan, register harus TERSEDIA (tidak ada di unavailable list)
        $unavailableBefore = DistribusiController::getUnavailableRegisterIds();
        $this->assertNotContains($register->id, $unavailableBefore);

        // Buat transaksi distribusi 1 dengan status 'Dalam Pengiriman' (belum di-oke oleh unit 1)
        $dist1 = Distribusi::create([
            'kode'               => 'DST-TEST-' . time() . '-001',
            'tanggal_distribusi' => now()->format('Y-m-d'),
            'unit_id'            => $unit1->id,
            'status'             => 'Dalam Pengiriman',
            'bast_nomor'         => '032 / 998 / 430.10.7 / 2026',
        ]);
        $item1 = DistribusiItem::create([
            'distribusi_id' => $dist1->id,
            'astap_id'      => $astap->id,
            'qty'           => 1,
            'qty_acc'       => 1,
        ]);
        DistribusiItemRegister::create([
            'distribusi_item_id' => $item1->id,
            'astap_register_id'  => $register->id,
        ]);

        // KASUS 1: Register yang sedang 'Dalam Pengiriman' HARUS masuk ke getUnavailableRegisterIds
        $unavailableAfter = DistribusiController::getUnavailableRegisterIds();
        $this->assertContains($register->id, $unavailableAfter);

        // Untuk transaksi lain (misal pengajuan distribusi 2 oleh Sub Admin 2), register ini TIDAK BOLEH tersedia
        $dist2 = Distribusi::create([
            'kode'               => 'DST-TEST-' . time() . '-002',
            'tanggal_distribusi' => now()->format('Y-m-d'),
            'unit_id'            => $unit2->id,
            'status'             => 'Menunggu Konfirmasi',
        ]);
        $unavailableForDist2 = DistribusiController::getUnavailableRegisterIds($dist2->id);
        $this->assertContains($register->id, $unavailableForDist2);

        // Namun untuk transaksi 1 itu sendiri (saat diedit ulang oleh admin), register miliknya tetap diizinkan
        $unavailableForDist1 = DistribusiController::getUnavailableRegisterIds($dist1->id);
        $this->assertNotContains($register->id, $unavailableForDist1);

        // Cleanup
        $dist1->delete();
        $dist2->delete();
        $register->delete();
    }

    /**
     * Kasus 2: Concurrency Collision Protection
     * Jika NIBAR sudah diambil/dikirim oleh Admin ke Unit A, Master Admin yang submit beberapa detik
     * kemudian untuk Unit B HARUS DITOLAK (HTTP 422) dan data Unit A tidak boleh tertimpa.
     */
    public function test_concurrent_submission_with_same_nibar_is_rejected(): void
    {
        $admin = User::whereIn('role', ['admin', 'master_admin'])->first();
        $unitA = Unit::where('nama', 'LIKE', '%Keuangan%')->first() ?? Unit::first();
        $unitB = Unit::where('nama', 'LIKE', '%Kepegawaian%')->first() ?? Unit::skip(1)->first();

        $astap = Astap::first();

        $testNibar = 'TESTNIBAR-' . time() . '-002';
        $register = AstapRegister::create([
            'astap_id'        => $astap->id,
            'tahun_perolehan' => 2026,
            'no_register_int' => 9992,
            'no_register'     => $testNibar,
            'nibar'           => $testNibar,
            'ruang_pemegang'  => null,
            'status'          => 'Tersedia',
            'kondisi'         => 'Baik',
        ]);

        // 1. Admin submit pertama kali ke Unit A (Berhasil)
        $payloadAdmin = [
            'kode'               => 'DST-TEST-' . time() . '-A',
            'unit_id'            => $unitA->id,
            'tanggal_distribusi' => now()->format('Y-m-d'),
            'pj_nama'            => 'PJ Unit A',
            'pj_nip'             => '19800101',
            'pj_jabatan'         => 'Kepala Unit A',
            'status'             => 'Dalam Pengiriman',
            'keterangan'         => 'Penyerahan unit ke Bagian A',
            'items'              => [
                [
                    'astap_id'     => $astap->id,
                    'nama_barang'  => $astap->nama_barang,
                    'qty'          => 1,
                    'qty_acc'      => 1,
                    'keterangan'   => 'Unit Aset A',
                    'register_ids' => [$register->id],
                ]
            ]
        ];

        $responseAdmin = $this->actingAs($admin)->postJson(route('distribusi.save'), $payloadAdmin);
        $responseAdmin->assertStatus(200);
        $this->assertTrue($responseAdmin->json('success'));

        // Pastikan register fisik ter-update ke Unit A
        $register->refresh();
        $this->assertEquals($unitA->id, $register->unit_id);
        $this->assertEquals($unitA->nama, $register->ruang_pemegang);
        $this->assertEquals('Tidak Tersedia', $register->status);

        // 2. Master Admin submit beberapa detik kemudian untuk NIBAR yang sama ke Unit B
        $payloadMasterAdmin = [
            'kode'               => 'DST-TEST-' . time() . '-B',
            'unit_id'            => $unitB->id,
            'tanggal_distribusi' => now()->format('Y-m-d'),
            'pj_nama'            => 'PJ Unit B',
            'pj_nip'             => '19850101',
            'pj_jabatan'         => 'Kepala Unit B',
            'status'             => 'Dalam Pengiriman',
            'keterangan'         => 'Penyerahan unit ke Bagian B',
            'items'              => [
                [
                    'astap_id'     => $astap->id,
                    'nama_barang'  => $astap->nama_barang,
                    'qty'          => 1,
                    'qty_acc'      => 1,
                    'keterangan'   => 'Unit Aset B',
                    'register_ids' => [$register->id], // NIBAR YANG SAMA!
                ]
            ]
        ];

        $responseMaster = $this->actingAs($admin)->postJson(route('distribusi.save'), $payloadMasterAdmin);
        // HARUS DITOLAK DENGAN HTTP 422
        $responseMaster->assertStatus(422);
        $this->assertFalse($responseMaster->json('success'));
        $this->assertStringContainsString('tidak dapat didistribusikan ganda', $responseMaster->json('message'));
        $this->assertContains($register->id, $responseMaster->json('conflicted_register_ids'));

        // PASTIKAN DATA UNIT A TIDAK TERTIMPA
        $register->refresh();
        $this->assertEquals($unitA->id, $register->unit_id, 'Unit ID tidak boleh tertimpa ke Unit B!');
        $this->assertEquals($unitA->nama, $register->ruang_pemegang, 'Ruang pemegang tidak boleh tertimpa ke Unit B!');

        // Cleanup
        $distA = Distribusi::where('kode', $payloadAdmin['kode'])->first();
        if ($distA) $distA->delete();
        $register->delete();
    }

    public function test_distribusi_rejection_requires_alasan_penolakan(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $unit = Unit::first() ?? Unit::create(['nama' => 'Unit Uji Tolak', 'tipe' => 'Ruangan', 'kepala' => 'Dr. Uji']);
        $astap = Astap::first();

        // 1. Submit status Ditolak TANPA alasan_penolakan -> Harus 422
        $payloadTanpaAlasan = [
            'kode'               => 'DST-TEST-REJECT-1',
            'unit_id'            => $unit->id,
            'tanggal_distribusi' => now()->format('Y-m-d'),
            'pj_nama'            => 'PJ Unit',
            'pj_nip'             => '19800101',
            'pj_jabatan'         => 'Kepala Unit',
            'status'             => 'Ditolak',
            'keterangan'         => '-',
            'alasan_penolakan'   => '', // KOSONG
            'items'              => [
                [
                    'astap_id'     => $astap->id,
                    'nama_barang'  => $astap->nama_barang,
                    'qty'          => 1,
                    'qty_acc'      => 0,
                    'register_ids' => [],
                ]
            ]
        ];

        $res1 = $this->actingAs($admin)->postJson(route('distribusi.save'), $payloadTanpaAlasan);
        $res1->assertStatus(422);
        $this->assertFalse($res1->json('success'));
        $this->assertStringContainsString('Alasan Penolakan harus diisi', $res1->json('message'));

        // 2. Submit status Ditolak DENGAN alasan_penolakan -> Harus 200 & tersimpan di DB
        $payloadDenganAlasan = $payloadTanpaAlasan;
        $payloadDenganAlasan['kode'] = 'DST-TEST-REJECT-2';
        $payloadDenganAlasan['alasan_penolakan'] = 'Stok barang di gudang sedang dialokasikan untuk penanganan darurat.';

        $res2 = $this->actingAs($admin)->postJson(route('distribusi.save'), $payloadDenganAlasan);
        $res2->assertStatus(200);
        $savedId = $res2->json('data.id');
        $distSaved = Distribusi::find($savedId);
        $this->assertNotNull($distSaved);
        $this->assertEquals('Ditolak', $distSaved->status);
        $this->assertEquals('Stok barang di gudang sedang dialokasikan untuk penanganan darurat.', $distSaved->alasan_penolakan);

        // Cleanup
        $distSaved->delete();
    }
}

