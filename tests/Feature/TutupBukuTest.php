<?php

namespace Tests\Feature;

use App\Models\Astap;
use App\Models\JenisAstap;
use App\Models\PeriodeTutupBuku;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TutupBukuTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticateMasterAdmin(): User
    {
        $user = User::factory()->create([
            'role' => 'master_admin',
        ]);
        $this->actingAs($user);
        return $user;
    }

    protected function authenticateAdmin(): User
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);
        $this->actingAs($user);
        return $user;
    }

    public function test_parse_triwulan_works_correctly()
    {
        $this->assertEquals(1, PeriodeTutupBuku::parseTriwulan('TW I'));
        $this->assertEquals(1, PeriodeTutupBuku::parseTriwulan('TW 1'));
        $this->assertEquals(2, PeriodeTutupBuku::parseTriwulan('TW II'));
        $this->assertEquals(2, PeriodeTutupBuku::parseTriwulan(2));
        $this->assertEquals(3, PeriodeTutupBuku::parseTriwulan('Triwulan III'));
        $this->assertEquals(4, PeriodeTutupBuku::parseTriwulan('TW IV'));
        $this->assertEquals(0, PeriodeTutupBuku::parseTriwulan('Tahunan'));
        $this->assertEquals(0, PeriodeTutupBuku::parseTriwulan(0));
    }

    public function test_get_tutup_buku_status_returns_periods_and_summary_metrics()
    {
        $this->authenticateMasterAdmin();

        $jenis = JenisAstap::create([
            'nama_jenis' => 'Peralatan Medis',
            'jenis' => '1.3.2.07',
            'sub_rincian_objek' => '1.3.2.07',
            'uraian_sub_rincian' => 'Alat Kedokteran dan Kesehatan',
            'sub_sub_rincian_objek' => '1.3.2.07.01.01',
            'uraian_sub_sub_rincian' => 'USG 4D Doppler',
            'kelompok_kib' => 'KIB B',
        ]);

        Astap::create([
            'jenis_astap_id' => $jenis->id,
            'nama_barang' => 'USG 4D Doppler',
            'tahun_perolehan' => 2026,
            'triwulan' => 'TW I',
            'harga_satuan' => 250000000,
            'jumlah_volume' => 2,
            'total_realisasi' => 500000000,
            'is_deleted' => 0,
        ]);

        $response = $this->getJson('/api/tutup-buku/status?tahun=2026');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'tahun' => 2026,
            'is_annual_locked' => false,
        ]);

        $data = $response->json();
        $this->assertCount(5, $data['periods']); // TW 1, 2, 3, 4, Tahunan
        $tw1 = collect($data['periods'])->firstWhere('triwulan', 1);
        $this->assertEquals(1, $tw1['total_item']);
        $this->assertEquals(2, $tw1['total_volume']);
        $this->assertEquals(500000000, $tw1['total_nominal']);
        $this->assertFalse($tw1['is_locked']);
    }

    public function test_lock_and_unlock_period_via_api()
    {
        $admin = $this->authenticateMasterAdmin();

        // 1. Lock TW 1
        $lockRes = $this->postJson('/api/tutup-buku/lock', [
            'tahun' => 2026,
            'triwulan' => 1,
            'nomor_bar_bpkad' => '000.2/BAR-REKON-TW1/BPKAD/2026',
            'tanggal_tutup' => '2026-03-31',
            'keterangan' => 'Selesai rekonsiliasi triwulan 1 dengan BPKAD',
        ]);

        $lockRes->assertStatus(200);
        $lockRes->assertJson(['success' => true]);

        $this->assertTrue(PeriodeTutupBuku::isLocked(2026, 1));
        $this->assertFalse(PeriodeTutupBuku::isLocked(2026, 2));

        // 2. Unlock TW 1
        $unlockRes = $this->postJson('/api/tutup-buku/unlock', [
            'tahun' => 2026,
            'triwulan' => 1,
            'alasan_unlock' => 'Perbaikan posting belanja modal berdasarkan Nota Dinas BPKAD No. 123',
        ]);

        $unlockRes->assertStatus(200);
        $unlockRes->assertJson(['success' => true]);
        $this->assertFalse(PeriodeTutupBuku::isLocked(2026, 1));
    }

    public function test_annual_closing_effectively_locks_all_triwulans()
    {
        $this->authenticateMasterAdmin();

        PeriodeTutupBuku::create([
            'tahun' => 2026,
            'triwulan' => 0,
            'is_locked' => true,
            'nomor_bar_bpkad' => '000.1/BAR-TAHUNAN/BPKAD/2026',
            'tanggal_tutup' => '2026-12-31',
        ]);

        $this->assertTrue(PeriodeTutupBuku::isLocked(2026, 0));
        $this->assertTrue(PeriodeTutupBuku::isLocked(2026, 1));
        $this->assertTrue(PeriodeTutupBuku::isLocked(2026, 2));
        $this->assertTrue(PeriodeTutupBuku::isLocked(2026, 3));
        $this->assertTrue(PeriodeTutupBuku::isLocked(2026, 4));
    }

    public function test_cannot_update_or_delete_astap_in_locked_period()
    {
        $this->authenticateMasterAdmin();

        $jenis = JenisAstap::create([
            'nama_jenis' => 'Peralatan Medis',
            'jenis' => '1.3.2.07',
            'sub_rincian_objek' => '1.3.2.07',
            'uraian_sub_rincian' => 'Alat Kedokteran dan Kesehatan',
            'sub_sub_rincian_objek' => '1.3.2.07.01.01',
            'uraian_sub_sub_rincian' => 'Ventilator ICU',
            'kelompok_kib' => 'KIB B',
        ]);

        $astap = Astap::create([
            'jenis_astap_id' => $jenis->id,
            'nama_barang' => 'Ventilator ICU High-End',
            'tahun_perolehan' => 2026,
            'triwulan' => 'TW I',
            'harga_satuan' => 400000000,
            'jumlah_volume' => 1,
            'total_realisasi' => 400000000,
            'is_deleted' => 0,
        ]);

        // Kunci periode TW 1
        PeriodeTutupBuku::create([
            'tahun' => 2026,
            'triwulan' => 1,
            'is_locked' => true,
            'nomor_bar_bpkad' => '000.2/BAR-TW1/BPKAD/2026',
            'tanggal_tutup' => '2026-03-31',
        ]);

        // Coba Update via PUT /astap/{id} -> Ditolak (422)
        $putRes = $this->putJson('/astap/' . $astap->id, [
            'nama_barang' => 'Ventilator ICU Revised',
            'harga_satuan' => 450000000,
        ]);

        $putRes->assertStatus(422);
        $putRes->assertJsonFragment(['success' => false]);
        $this->assertStringContainsString('DITUTUP BUKU', $putRes->json('message'));

        // Coba Hapus via DELETE /astap/{id} -> Ditolak (422)
        $delRes = $this->deleteJson('/astap/' . $astap->id);

        $delRes->assertStatus(422);
        $delRes->assertJsonFragment(['success' => false, 'is_blocked' => true]);
        $this->assertStringContainsString('DITUTUP BUKU', $delRes->json('message'));
        $this->assertEquals(0, $astap->fresh()->is_deleted);
    }

    public function test_can_access_tutup_buku_page_without_sidebar()
    {
        $this->authenticateMasterAdmin();

        $response = $this->get('/tutup-buku');

        $response->assertStatus(200);
        $response->assertViewIs('pages.tutup_buku.index');
        $response->assertSee('Manajemen Tutup Buku BMD');
        $response->assertSee('Tutup Buku Triwulan');
        $response->assertSee('Tutup Buku Tahunan');
    }
}
