<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AstapBastTriwulan;

class AstapBastTriwulanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultDocs = [
            [
                'tahun'          => '2026',
                'triwulan'       => 'TW1',
                'nomor_surat'    => '000.2.3.2/112/430.10.7/2026',
                'tanggal_bast'   => '2026-03-31',
                'lokasi'         => 'Rumah Sakit Umum Daerah dr.H.Koesnandi Kabupaten Bondowoso',
                'pihak1_nama'    => 'dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR.',
                'pihak1_nip'     => '19771002 200604 1 006',
                'pihak1_jabatan' => 'Pejabat Pembuat Komitmen (PPK) / Penerima Hasil Pengadaan RSUD dr.H.Koesnandi',
                'pihak2_nama'    => 'BUDI HARTONO,S.Sos',
                'pihak2_nip'     => '19760229 200801 1 010',
                'pihak2_jabatan' => 'Pengurus Barang Aset Pada RSUD dr.H.Koesnandi Kabupaten Bondowoso',
                'direktur_nama'  => 'dr. DIAN ARISANDI, M.Kes',
                'direktur_nip'   => '19730514 200212 2 003',
                'status'         => 'Telah Ditandatangani BSrE',
                'signed'         => true,
                'tgl_signed'     => '31/03/2026 15:40 WIB',
                'qr_hash'        => 'BSRE-KOESNANDI-BAST-TW1-2026-0914',
                'catatan'        => 'Rekonsiliasi Belanja Modal Aset Tetap Triwulan I Tahun Anggaran 2026 Selesai 100%.',
            ],
            [
                'tahun'          => '2026',
                'triwulan'       => 'TW2',
                'nomor_surat'    => '000.2.3.2/224/430.10.7/2026',
                'tanggal_bast'   => '2026-06-30',
                'lokasi'         => 'Rumah Sakit Umum Daerah dr.H.Koesnandi Kabupaten Bondowoso',
                'pihak1_nama'    => 'dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR.',
                'pihak1_nip'     => '19771002 200604 1 006',
                'pihak1_jabatan' => 'Pejabat Pembuat Komitmen (PPK) / Penerima Hasil Pengadaan RSUD dr.H.Koesnandi',
                'pihak2_nama'    => 'BUDI HARTONO,S.Sos',
                'pihak2_nip'     => '19760229 200801 1 010',
                'pihak2_jabatan' => 'Pengurus Barang Aset Pada RSUD dr.H.Koesnandi Kabupaten Bondowoso',
                'direktur_nama'  => 'dr. DIAN ARISANDI, M.Kes',
                'direktur_nip'   => '19730514 200212 2 003',
                'status'         => 'Telah Ditandatangani BSrE',
                'signed'         => true,
                'tgl_signed'     => '30/06/2026 14:32 WIB',
                'qr_hash'        => 'BSRE-KOESNANDI-BAST-TW2-2026-0887',
                'catatan'        => 'Rekonsiliasi Belanja Modal Aset Tetap Triwulan II (Semester 1) Tahun Anggaran 2026 Telah Diverifikasi BPKAD.',
            ],
            [
                'tahun'          => '2026',
                'triwulan'       => 'TW3',
                'nomor_surat'    => '000.2.3.2/318/430.10.7/2026',
                'tanggal_bast'   => '2026-09-30',
                'lokasi'         => 'Rumah Sakit Umum Daerah dr.H.Koesnandi Kabupaten Bondowoso',
                'pihak1_nama'    => 'dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR.',
                'pihak1_nip'     => '19771002 200604 1 006',
                'pihak1_jabatan' => 'Pejabat Pembuat Komitmen (PPK) / Penerima Hasil Pengadaan RSUD dr.H.Koesnandi',
                'pihak2_nama'    => 'BUDI HARTONO,S.Sos',
                'pihak2_nip'     => '19760229 200801 1 010',
                'pihak2_jabatan' => 'Pengurus Barang Aset Pada RSUD dr.H.Koesnandi Kabupaten Bondowoso',
                'direktur_nama'  => 'dr. DIAN ARISANDI, M.Kes',
                'direktur_nip'   => '19730514 200212 2 003',
                'status'         => 'Telah Ditandatangani BSrE',
                'signed'         => true,
                'tgl_signed'     => '30/09/2026 16:10 WIB',
                'qr_hash'        => 'BSRE-KOESNANDI-BAST-TW3-2026-0922',
                'catatan'        => 'Rekonsiliasi Belanja Modal Aset Tetap Triwulan III Selesai.',
            ],
            [
                'tahun'          => '2026',
                'triwulan'       => 'TW4',
                'nomor_surat'    => '000.2.3.2/415/430.10.7/2026',
                'tanggal_bast'   => '2026-12-31',
                'lokasi'         => 'Rumah Sakit Umum Daerah dr.H.Koesnandi Kabupaten Bondowoso',
                'pihak1_nama'    => 'dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR.',
                'pihak1_nip'     => '19771002 200604 1 006',
                'pihak1_jabatan' => 'Pejabat Pembuat Komitmen (PPK) / Penerima Hasil Pengadaan RSUD dr.H.Koesnandi',
                'pihak2_nama'    => 'BUDI HARTONO,S.Sos',
                'pihak2_nip'     => '19760229 200801 1 010',
                'pihak2_jabatan' => 'Pengurus Barang Aset Pada RSUD dr.H.Koesnandi Kabupaten Bondowoso',
                'direktur_nama'  => 'dr. DIAN ARISANDI, M.Kes',
                'direktur_nip'   => '19730514 200212 2 003',
                'status'         => 'Draft',
                'signed'         => false,
                'tgl_signed'     => null,
                'qr_hash'        => null,
                'catatan'        => 'Draft Dokumen BAST Triwulan IV Menunggu Realisasi Belanja Akhir Tahun.',
            ],
        ];

        foreach ($defaultDocs as $doc) {
            AstapBastTriwulan::updateOrCreate(
                ['tahun' => $doc['tahun'], 'triwulan' => $doc['triwulan']],
                $doc
            );
        }
    }
}
