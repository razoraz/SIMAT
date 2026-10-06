<?php

namespace Tests\Feature;

use App\Models\Astap;
use App\Models\AstapReklas;
use App\Models\JenisAstap;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditKoreksiTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticateAdmin(): User
    {
        $user = User::factory()->create([
            'role' => 'master_admin',
        ]);
        $this->actingAs($user);
        return $user;
    }

    public function test_audit_koreksi_index_page_can_be_accessed_by_admin()
    {
        $this->authenticateAdmin();

        $response = $this->get(route('audit_koreksi.index'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.audit_koreksi.index');
        $response->assertSee('Audit & Ledger Koreksi Nilai BMD');
        $response->assertSee('Koreksi Biasa (Internal)');
        $response->assertSee('Koreksi LKD (BPK RI)');
        $response->assertSee('Koreksi Manset (BPKAD)');
    }

    public function test_audit_koreksi_shows_and_differentiates_all_three_sub_koreksi_types()
    {
        $this->authenticateAdmin();

        $jenis = JenisAstap::first() ?? JenisAstap::create([
            'nama_jenis' => 'Peralatan Medis',
            'jenis' => '1.3.2.07',
            'sub_rincian_objek' => '1.3.2.07',
            'uraian_sub_rincian' => 'Alat Kedokteran dan Kesehatan',
            'sub_sub_rincian_objek' => '1.3.2.07.01.01',
            'uraian_sub_sub_rincian' => 'EKG Monitor',
            'kelompok_kib' => 'KIB B',
        ]);

        $astap = Astap::create([
            'jenis_astap_id' => $jenis->id,
            'nama_barang' => 'EKG Monitor Intensive',
            'nibar' => 'EKG-2026-001',
            'tahun_perolehan' => 2026,
            'harga_satuan' => 15000000,
            'jumlah_volume' => 1,
            'total_realisasi' => 15000000,
            'tahun_anggaran' => 2026,
            'sumber_dana' => 'belanja_modal',
        ]);

        // 1. Buat Koreksi Biasa
        $koreksiBiasa = AstapReklas::create([
            'astap_id' => $astap->id,
            'jenis_reklas' => 'KOREKSI_LAIN',
            'sub_koreksi' => 'biasa',
            'nilai_reklas' => 1000000,
            'tanggal_reklas' => '2026-02-10',
            'tahun' => 2026,
            'triwulan' => 1,
            'nomor_ba_reklas' => 'BA-INTERNAL-001',
            'keterangan' => 'Koreksi internal pembukuan',
            'asal_kib' => 'KIB B',
            'tujuan_kib' => 'KOREKSI',
        ]);

        // 2. Buat Koreksi LKD
        $koreksiLkd = AstapReklas::create([
            'astap_id' => $astap->id,
            'jenis_reklas' => 'KOREKSI_LAIN',
            'sub_koreksi' => 'lkd',
            'nilai_reklas' => 2500000,
            'tanggal_reklas' => '2026-03-15',
            'tahun' => 2026,
            'triwulan' => 1,
            'nomor_ba_reklas' => 'LHP-BPK-2026-09',
            'keterangan' => 'Temuan audit BPK RI kelebihan biaya',
            'asal_kib' => 'KIB B',
            'tujuan_kib' => 'KOREKSI',
        ]);

        // 3. Buat Koreksi Manset
        $koreksiManset = AstapReklas::create([
            'astap_id' => $astap->id,
            'jenis_reklas' => 'KOREKSI_LAIN',
            'sub_koreksi' => 'manset',
            'nilai_reklas' => 500000,
            'tanggal_reklas' => '2026-04-20',
            'tahun' => 2026,
            'triwulan' => 2,
            'nomor_ba_reklas' => 'BA-MANSET-BPKAD-44',
            'keterangan' => 'Penyelarasan kapitalisasi E-Manset BPKAD',
            'asal_kib' => 'KOREKSI',
            'tujuan_kib' => 'KIB B',
        ]);

        $response = $this->get(route('audit_koreksi.index', ['tahun' => 2026, 'triwulan' => 'all']));
        $response->assertStatus(200);

        // Verifikasi keberadaan masing-masing transaksi di view
        $response->assertSee('BA-INTERNAL-001');
        $response->assertSee('LHP-BPK-2026-09');
        $response->assertSee('BA-MANSET-BPKAD-44');

        // Verifikasi view data collections dipisahkan dengan tepat
        $response->assertViewHas('biasaKoreksi', function ($collection) use ($koreksiBiasa) {
            return $collection->contains('id', $koreksiBiasa->id);
        });

        $response->assertViewHas('lkdKoreksi', function ($collection) use ($koreksiLkd) {
            return $collection->contains('id', $koreksiLkd->id);
        });

        $response->assertViewHas('mansetKoreksi', function ($collection) use ($koreksiManset) {
            return $collection->contains('id', $koreksiManset->id);
        });
    }

    public function test_audit_koreksi_show_endpoint_returns_json_details()
    {
        $this->authenticateAdmin();

        $astap = Astap::first() ?? Astap::create([
            'nama_barang' => 'USG 4D Obstetri',
            'nibar' => 'USG-2026-099',
            'tahun_perolehan' => 2026,
            'harga_satuan' => 50000000,
            'jumlah_volume' => 1,
            'total_realisasi' => 50000000,
            'tahun_anggaran' => 2026,
            'sumber_dana' => 'belanja_modal',
        ]);

        $koreksi = AstapReklas::create([
            'astap_id' => $astap->id,
            'jenis_reklas' => 'KOREKSI_LAIN',
            'sub_koreksi' => 'lkd',
            'nilai_reklas' => 7500000,
            'tanggal_reklas' => '2026-05-12',
            'tahun' => 2026,
            'triwulan' => 2,
            'nomor_ba_reklas' => 'LHP-BPK/55/2026',
            'keterangan' => 'Rekomendasi LHP BPK',
            'alasan_reklas' => 'Pengurangan nilai sesuai hasil audit fisik BPK',
            'asal_kib' => 'KIB B',
            'tujuan_kib' => 'KOREKSI',
        ]);

        $response = $this->getJson(route('audit_koreksi.show', $koreksi->id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $koreksi->id,
                'sub_koreksi' => 'lkd',
                'sub_koreksi_label' => 'Koreksi LKD (BPK RI)',
                'tipe_koreksi' => 'kurang',
                'nilai_reklas' => 7500000,
                'nomor_ba_reklas' => 'LHP-BPK/55/2026',
            ],
        ]);
    }

    public function test_audit_koreksi_export_generates_csv_stream()
    {
        $this->authenticateAdmin();

        $response = $this->get(route('audit_koreksi.export', ['tahun' => 2026]));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('Rekap_Audit_Koreksi_Nilai_BMD', $response->headers->get('Content-Disposition'));
    }
}
