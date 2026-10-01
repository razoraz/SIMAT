<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Astap;
use App\Models\AstapKemitraan;
use App\Models\JenisAstap;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class KemitraanUploadTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.sqlite.database' => database_path('database.sqlite')]);
        \DB::purge('sqlite');
    }

    public function test_store_and_update_kemitraan_with_bast_file(): void
    {
        Storage::fake('public');

        $admin = User::whereIn('role', ['admin', 'master_admin'])->first();
        if (!$admin) {
            $admin = User::first();
        }
        $this->assertNotNull($admin, 'User admin required for test');

        $jenisAstap = JenisAstap::first();
        $this->assertNotNull($jenisAstap, 'JenisAstap required for test');

        // 1. Simpan Kemitraan Baru dengan File BAST
        $fileDummy = UploadedFile::fake()->create('BAST_PKS_2026.pdf', 1024, 'application/pdf');

        $payload = [
            'nama_barang'          => 'Unit Alat Laboratorium Hematology Analyzer Test',
            'jenis_astap_id'       => $jenisAstap->id,
            'tahun_perolehan'      => 2026,
            'jumlah_volume'        => 1,
            'satuan'               => 'Unit',
            'total_realisasi'      => 75000000,
            'triwulan'             => 'TW IV',
            'skema_kemitraan'      => 'KSO',
            'mitra_nama'           => 'PT. Mitra Medika Farma',
            'mitra_pimpinan'       => 'Dr. H. Bambang Soedjarwo',
            'mitra_alamat'         => 'Jl. Raya Darmo No. 45 Surabaya',
            'ppk_nama'             => 'BUDI HARTONO, S.Sos',
            'ppk_nip'              => '19760229 200801 1 010',
            'nomor_pks'            => '000.2.3.2/PKS-KSO/430.10.7/2026',
            'tanggal_pks'          => date('d/m/Y'),
            'tanggal_mulai'        => date('d/m/Y'),
            'tanggal_selesai'      => date('d/m/Y', strtotime('+3 years')),
            'kemitraan_keterangan' => 'Pengadaan KSO Reagen & Analyzer',
            'dokumen_file'         => $fileDummy,
            'mesin_items'          => json_encode([
                [
                    'mesin_nama_barang' => 'Hematology Analyzer Sysmex',
                    'mesin_merk'        => 'Sysmex',
                    'mesin_type'        => 'XN-550',
                    'mesin_jumlah'      => 1,
                    'mesin_satuan'      => 'Unit',
                    'mesin_harga_satuan'=> 75000000,
                    'mesin_kondisi'     => 'Baik'
                ]
            ]),
            'spesifikasi_json'     => json_encode([
                'kategori_kib' => 'KIB B (Peralatan & Mesin)',
                'merk'         => 'Sysmex',
                'type'         => 'XN-550'
            ])
        ];

        $res = $this->actingAs($admin)->post('/astap/store-kemitraan', $payload, [
            'Accept' => 'application/json'
        ]);

        $res->assertStatus(200);
        $res->assertJson(['success' => true]);

        // Verifikasi database
        $astap = Astap::where('nama_barang', 'Unit Alat Laboratorium Hematology Analyzer Test')->latest()->first();
        $this->assertNotNull($astap);
        $this->assertEquals(75000000, (float)$astap->total_realisasi);

        $kemitraan = AstapKemitraan::where('astap_id', $astap->id)->first();
        $this->assertNotNull($kemitraan);
        $this->assertEquals('PT. Mitra Medika Farma', $kemitraan->mitra_nama);
        $this->assertEquals('Dr. H. Bambang Soedjarwo', $kemitraan->mitra_pimpinan);
        $this->assertEquals('Jl. Raya Darmo No. 45 Surabaya', $kemitraan->mitra_alamat);
        $this->assertNotNull($kemitraan->dokumen_path);

        // Verifikasi fisik file tersimpan di storage fake
        Storage::disk('public')->assertExists($kemitraan->dokumen_path);

        // Verifikasi spesifikasi_json astap juga memuat dokumen_path
        $spec = is_array($astap->spesifikasi_json) ? $astap->spesifikasi_json : json_decode($astap->spesifikasi_json, true);
        $this->assertEquals($kemitraan->dokumen_path, $spec['dokumen_path']);

        $savedOldPath = $kemitraan->dokumen_path;

        // 2. Uji Update Tanpa Mengunggah File Baru (Pastikan dokumen_path lama tidak terhapus)
        $updatePayload = [
            'nama_barang'          => 'Unit Alat Laboratorium Hematology Analyzer Test (Updated)',
            'jenis_astap_id'       => $jenisAstap->id,
            'tahun_perolehan'      => 2026,
            'jumlah_volume'        => 1,
            'satuan'               => 'Unit',
            'total_realisasi'      => 80000000,
            'triwulan'             => 'TW IV',
            'skema_kemitraan'      => 'KSO',
            'mitra_nama'           => 'PT. Mitra Medika Farma',
            'mitra_pimpinan'       => 'Dr. H. Bambang Soedjarwo, Sp.PK',
            'mitra_alamat'         => 'Jl. Raya Darmo No. 45 Surabaya Baru',
            'ppk_nama'             => 'BUDI HARTONO, S.Sos',
            'ppk_nip'              => '19760229 200801 1 010',
            'nomor_pks'            => '000.2.3.2/PKS-KSO/430.10.7/2026-REV',
            'tanggal_pks'          => date('d/m/Y'),
            'kemitraan_keterangan' => 'Revisi nilai realisasi',
        ];

        $updateRes = $this->actingAs($admin)->post("/astap/update-kemitraan/{$astap->id}", $updatePayload, [
            'Accept' => 'application/json'
        ]);

        $updateRes->assertStatus(200);
        $updateRes->assertJson(['success' => true]);

        $kemitraan->refresh();
        $this->assertEquals($savedOldPath, $kemitraan->dokumen_path, 'dokumen_path lama harus tetap bertahan jika tidak ada file baru yang diunggah');

        // 3. Uji Update Dengan Mengunggah File Baru
        $newFileDummy = UploadedFile::fake()->create('BAST_REVISI_FINAL.pdf', 2048, 'application/pdf');
        $updateWithFilePayload = array_merge($updatePayload, [
            'dokumen_file' => $newFileDummy
        ]);

        $updateWithFileRes = $this->actingAs($admin)->post("/astap/update-kemitraan/{$astap->id}", $updateWithFilePayload, [
            'Accept' => 'application/json'
        ]);

        $updateWithFileRes->assertStatus(200);
        $kemitraan->refresh();
        $this->assertNotEquals($savedOldPath, $kemitraan->dokumen_path, 'dokumen_path harus terupdate dengan file baru');
        Storage::disk('public')->assertExists($kemitraan->dokumen_path);

        // Bersihkan data dummy test
        $astap->registers()->delete();
        $kemitraan->delete();
        $astap->delete();
    }

    public function test_store_kemitraan_without_bast_file(): void
    {
        $admin = User::whereIn('role', ['admin', 'master_admin'])->first() ?? User::first();
        $jenisAstap = JenisAstap::first();

        $payload = [
            'nama_barang'          => 'Unit Genset Cadangan Kemitraan',
            'jenis_astap_id'       => $jenisAstap->id,
            'tahun_perolehan'      => 2026,
            'jumlah_volume'        => 1,
            'satuan'               => 'Unit',
            'total_realisasi'      => 120000000,
            'triwulan'             => 'TW I',
            'skema_kemitraan'      => 'Sewa',
            'mitra_nama'           => 'PT. Daya Energi Pratama',
            'mitra_pimpinan'       => 'Ir. Hendra Kusuma',
            'mitra_alamat'         => 'Kawasan Industri Rungkut Surabaya',
            'nomor_pks'            => '000.2.3.2/PKS-SEWA/GENSET/2026',
            'tanggal_pks'          => date('d/m/Y'),
            'kemitraan_keterangan' => 'Sewa genset backup gedung rawat inap',
            'mesin_items'          => [
                [
                    'mesin_nama_barang' => 'Genset Silent 250 KVA',
                    'mesin_merk'        => 'Cummins',
                    'mesin_type'        => '6CTAA8.3-G2',
                    'mesin_jumlah'      => 1,
                    'mesin_satuan'      => 'Unit',
                    'mesin_harga_satuan'=> 120000000,
                    'mesin_kondisi'     => 'Baik'
                ]
            ],
            'spesifikasi_json'     => [
                'kategori_kib' => 'KIB B (Peralatan & Mesin)',
                'merk'         => 'Cummins',
                'type'         => '6CTAA8.3-G2'
            ]
        ];

        $res = $this->actingAs($admin)->post('/astap/store-kemitraan', $payload, [
            'Accept' => 'application/json'
        ]);

        $res->assertStatus(200);
        $res->assertJson(['success' => true]);

        $astap = Astap::where('nama_barang', 'Unit Genset Cadangan Kemitraan')->latest()->first();
        $this->assertNotNull($astap);
        $kemitraan = AstapKemitraan::where('astap_id', $astap->id)->first();
        $this->assertNotNull($kemitraan);
        $this->assertNull($kemitraan->dokumen_path);

        // Bersihkan data
        $astap->registers()->delete();
        $kemitraan->delete();
        $astap->delete();
    }

    public function test_master_kemitraan_index_and_form_views(): void
    {
        $admin = User::whereIn('role', ['admin', 'master_admin'])->first();

        // 1. Cek halaman form input kemitraan
        $formRes = $this->actingAs($admin)->get(route('astap.create_kemitraan'));
        $formRes->assertStatus(200);
        $formRes->assertSee('Bentuk Skema Kemitraan Sesuai Permendagri 108 / SAP');
        $formRes->assertSee('Unggah Berkas Dokumen BAST / PKS Kerja Sama');
        $formRes->assertDontSee('Kolom 22');
        $formRes->assertDontSee('Kolom 23');

        // 2. Cek halaman master data kemitraan
        $masterRes = $this->actingAs($admin)->get(route('master.kemitraan'));
        $masterRes->assertStatus(200);
        $masterRes->assertDontSee('Kolom 22');
        $masterRes->assertDontSee('Kolom 23');
    }
}

