<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Menambahkan User Seeders (Master Admin, Admin Operasional, dan 55 Sub Admin Kepala Unit RSUD)
     */
    public function run(): void
    {
        // 1. Master Admin System
        User::updateOrCreate(
            ['email' => 'masteradmin@asimat.com'],
            [
                'name' => 'Master Admin System',
                'nip' => '19820315 200604 1 008',
                'password' => Hash::make('password123'),
                'role' => 'master_admin',
                'unit' => 'Direksi & SIMRS',
                'penugasan' => 'Wewenang Penuh: Kontrol seluruh sistem, database, audit aset, dan hak akses',
                'status' => 'Aktif',
                'deskripsi' => 'Master Admin System - Kontrol Penuh Sistem SIMAT-RK',
            ]
        );

        // 2. Admin Operasional
        User::updateOrCreate(
            ['email' => 'admin@asimat.com'],
            [
                'name' => 'Admin Operasional SIMAT',
                'nip' => '19870822 201101 1 003',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'unit' => 'Bagian Umum & Aset',
                'penugasan' => 'Wewenang Operasional: Pengelolaan inventaris ASTAP, verifikasi pengadaan, distribusi & BAST',
                'status' => 'Aktif',
                'deskripsi' => 'Admin Operasional - Pengelola Inventaris & Distribusi Aset',
            ]
        );

        // 3. User Sub Master Dummy
        User::updateOrCreate(
            ['email' => 'subadmin@asimat.com'],
            [
                'name' => 'User Sub Master (Universal)',
                'nip' => '19920510 201802 2 005',
                'password' => Hash::make('password123'),
                'role' => 'sub_admin',
                'unit' => 'Semua Unit Paviliun',
                'penugasan' => 'Wewenang Unit: Pengajuan permohonan aset unit, pemantauan barang, & perbaikan',
                'status' => 'Aktif',
                'deskripsi' => 'User Sub Master Universal',
            ]
        );

        // 4. 55 Sub Admin Kepala Unit / Penanggung Jawab Ruangan RSUD
        User::updateOrCreate(
            ['email' => 'Yuspriyatna@yahoo.co.id'],
            [
                'name' => 'dr. YUS PRIYATNA ADRYANTO. Sp.P, FISR',
                'nip' => '197710022006041006',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Direktur Dan Wadir',
                'penugasan' => 'Sub Admin & Penanggung Jawab Direktur Dan Wadir (Direktur RSUD Dr. H. Koesnandi)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Direktur Dan Wadir (Direktur RSUD Dr. H. Koesnandi)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'stjumaroh73@gmail.com'],
            [
                'name' => 'ST ZUMAROH, SH.Msi',
                'nip' => '197303011998092001',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Bagian Kepegawaian',
                'penugasan' => 'Sub Admin & Penanggung Jawab Bagian Kepegawaian (Subkor. Kepegawaian & Pengembangan SDM)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Bagian Kepegawaian (Subkor. Kepegawaian & Pengembangan SDM)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'etsieveraningsih@gmail.com'],
            [
                'name' => 'ETSIE VERANINGSIH, SE, M.Si',
                'nip' => '197602072002122001',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Bagian Keuangan',
                'penugasan' => 'Sub Admin & Penanggung Jawab Bagian Keuangan (Kepala Bagian Keuangan)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Bagian Keuangan (Kepala Bagian Keuangan)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'yunarsooppo@gmail.com'],
            [
                'name' => 'YUNARSO, S.Kep.Ners',
                'nip' => '196904121995031003',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Bidang Keperawatan',
                'penugasan' => 'Sub Admin & Penanggung Jawab Bidang Keperawatan (Subkor. Pemeliharaan & Pengembangan Fasilitas Keperawatan)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Bidang Keperawatan (Subkor. Pemeliharaan & Pengembangan Fasilitas Keperawatan)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'hidayat92@yahoo.com'],
            [
                'name' => 'NURUL HIDAYAT, SKM., M.Kes',
                'nip' => '197707052003121009',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Bagian Perencanaan dan Pengembangan',
                'penugasan' => 'Sub Admin & Penanggung Jawab Bagian Perencanaan dan Pengembangan (Analis Kebijakan Ahli Muda | Anggota Sub Komite Keselamatan dan Keamanan Komite Keselamatan dan Kesehatan Kerja | Kabag. Perencanaan & Penyusunan Program)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Bagian Perencanaan dan Pengembangan (Analis Kebijakan Ahli Muda | Anggota Sub Komite Keselamatan dan Keamanan Komite Keselamatan dan Kesehatan Kerja | Kabag. Perencanaan & Penyusunan Program)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'bd290276@gmail.com'],
            [
                'name' => 'BUDI HARTONO, S. Sos',
                'nip' => '197602292008011010',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Bagian Rumah Tangga & Inst Perbekalan',
                'penugasan' => 'Sub Admin & Penanggung Jawab Bagian Rumah Tangga & Inst Perbekalan (Anggota Satuan Pengawas Internal | Anggota Sub Komite Penanggulangan Bencana (Emengency) Komite Keselamatan dan Kesehatan Kerja | Pengolah Pemanfaatan Barang Milik Daerah)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Bagian Rumah Tangga & Inst Perbekalan (Anggota Satuan Pengawas Internal | Anggota Sub Komite Penanggulangan Bencana (Emengency) Komite Keselamatan dan Kesehatan Kerja | Pengolah Pemanfaatan Barang Milik Daerah)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'budiantoeko63@yahoo.co.id'],
            [
                'name' => 'EKO BUDIANTO, SP. M.M.',
                'nip' => '197711261999011001',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Bagian Umum',
                'penugasan' => 'Sub Admin & Penanggung Jawab Bagian Umum (Kepala Bagian Umum)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Bagian Umum (Kepala Bagian Umum)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'dr.sintaanggraini@gmail.com'],
            [
                'name' => 'dr. SINTA AGITA ANGGRAINI',
                'nip' => '198312162009022008',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Bidang Pelayanan Medik',
                'penugasan' => 'Sub Admin & Penanggung Jawab Bidang Pelayanan Medik (Kepala Bidang Pelayanan Medik)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Bidang Pelayanan Medik (Kepala Bidang Pelayanan Medik)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'iidrosyidah2015@gmail.com'],
            [
                'name' => 'I\'ID ROSYIDAH, S.Kep.Ns',
                'nip' => '197608061999032002',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'ICU',
                'penugasan' => 'Sub Admin & Penanggung Jawab ICU (Kepala Ruang ICU)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin ICU (Kepala Ruang ICU)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'adhi_dr@yahoo.co.id'],
            [
                'name' => 'dr. ADHI SUDARMADJI',
                'nip' => '198410272009021003',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'IGD',
                'penugasan' => 'Sub Admin & Penanggung Jawab IGD (Kepala IGD)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin IGD (Kepala IGD)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'siswantoheri80@gmail.com'],
            [
                'name' => 'HERI SISWANTO, S.Kep.Ns., M.Kep',
                'nip' => '198009022005011005',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'ICCU',
                'penugasan' => 'Sub Admin & Penanggung Jawab ICCU (Kepala Ruang ICCU)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin ICCU (Kepala Ruang ICCU)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'edhipurwanto73@gmil.com'],
            [
                'name' => 'EDHI PURWANTO, S.Kep.Ns',
                'nip' => '197305181995031002',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Inst. Bedah Sentral',
                'penugasan' => 'Sub Admin & Penanggung Jawab Inst. Bedah Sentral (Kepala Ruang IBS)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Inst. Bedah Sentral (Kepala Ruang IBS)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'saifulwalid@gmail.com'],
            [
                'name' => 'SAIFUL WALID, S.Kep, Ns, M.MKes',
                'nip' => '197001051996031004',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Inst. CSSD & Loundry',
                'penugasan' => 'Sub Admin & Penanggung Jawab Inst. CSSD & Loundry (Kepala Instalasi CSSD Laundry)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Inst. CSSD & Loundry (Kepala Instalasi CSSD Laundry)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'indri.hardini.apt@gmail.com'],
            [
                'name' => 'INDRI HARDINI, S. Farm. Apt',
                'nip' => '198610112015032002',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Inst. Farmasi',
                'penugasan' => 'Sub Admin & Penanggung Jawab Inst. Farmasi (Kepala Instalasi Farmasi)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Inst. Farmasi (Kepala Instalasi Farmasi)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'fitrianurrahmi@gmail.com'],
            [
                'name' => 'FITRIA NUR RAHMI, S.Gz',
                'nip' => '198008092006042022',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Inst. Gizi',
                'penugasan' => 'Sub Admin & Penanggung Jawab Inst. Gizi (Kepala Instalasi Gizi)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Inst. Gizi (Kepala Instalasi Gizi)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'priantodani17@gmail.com'],
            [
                'name' => 'DANI PRIANTO, ST',
                'nip' => '198310152006041010',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Inst. IPS RS',
                'penugasan' => 'Sub Admin & Penanggung Jawab Inst. IPS RS (Anggota Sub Komite Sistem Penunjang (Utilitas) Komite Keselamatan dan Kesehatan Kerja | Pengelola Penataan Sarana dan Prasarana)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Inst. IPS RS (Anggota Sub Komite Sistem Penunjang (Utilitas) Komite Keselamatan dan Kesehatan Kerja | Pengelola Penataan Sarana dan Prasarana)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'agusprasetiyo9@gmail.com'],
            [
                'name' => 'AGUS PRASETIYO, S.Si,M.Mkes',
                'nip' => '197407232002121003',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Inst. Laboratorium',
                'penugasan' => 'Sub Admin & Penanggung Jawab Inst. Laboratorium (Kepala Ruang Instalasi Laboratorium)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Inst. Laboratorium (Kepala Ruang Instalasi Laboratorium)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'Hartono5194@gmail.com'],
            [
                'name' => 'HARTONO ZUPRIADY, S.Tr.Kes',
                'nip' => '197805052003121005',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Inst. Radiologi',
                'penugasan' => 'Sub Admin & Penanggung Jawab Inst. Radiologi (Kaur Pelayanan Instalasi Radiologi)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Inst. Radiologi (Kaur Pelayanan Instalasi Radiologi)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'edyotnamsi@gmail.com'],
            [
                'name' => 'ISMANTO, A.Md.Kep',
                'nip' => '198901012025211300',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Inst. Rawat Jenazah',
                'penugasan' => 'Sub Admin & Penanggung Jawab Inst. Rawat Jenazah (Kaur Pelayanan Rawat Jenazah)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Inst. Rawat Jenazah (Kaur Pelayanan Rawat Jenazah)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'tiwipras59@yahoo.co.id'],
            [
                'name' => 'PRASTIWI, A.Md',
                'nip' => '199207272015032008',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Inst. Rekam Medik',
                'penugasan' => 'Sub Admin & Penanggung Jawab Inst. Rekam Medik (Kepala Instalasi Rekam Medik)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Inst. Rekam Medik (Kepala Instalasi Rekam Medik)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'sy41ful17@gmail.com'],
            [
                'name' => 'SYAIFUL ANWAR, S.Kep.Ns',
                'nip' => '197901172003121004',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Inst. Hemodialisa',
                'penugasan' => 'Sub Admin & Penanggung Jawab Inst. Hemodialisa (Kaur Instalasi Hemodialisa)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Inst. Hemodialisa (Kaur Instalasi Hemodialisa)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'fiqihwahyudiansyah@gmail.com'],
            [
                'name' => 'FIQIH WAHYUDIANSYAH, S.Kom',
                'nip' => '199301282019031002',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Inst. Informasi Teknologi ( IT )',
                'penugasan' => 'Sub Admin & Penanggung Jawab Inst. Informasi Teknologi ( IT ) (Kepala Instalasi IT)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Inst. Informasi Teknologi ( IT ) (Kepala Instalasi IT)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'ainynunik@gmail.com'],
            [
                'name' => 'Nur `Aini Lestari, S.KM',
                'nip' => '198806102022042001',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Inst. PKRS',
                'penugasan' => 'Sub Admin & Penanggung Jawab Inst. PKRS (Kepala Instalasi PKRS)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Inst. PKRS (Kepala Instalasi PKRS)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'agusjulianto79@gmail.com'],
            [
                'name' => 'AGUS JULIANTO, S.KL',
                'nip' => '197907182006041014',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Inst. Sanitasi',
                'penugasan' => 'Sub Admin & Penanggung Jawab Inst. Sanitasi (Kepala Instalasi Sanitasi)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Inst. Sanitasi (Kepala Instalasi Sanitasi)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'sitikhotimahrsu@gmail.com'],
            [
                'name' => 'SITI KHOTIMAH',
                'nip' => '197004112007012009',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Inst. Pengendali',
                'penugasan' => 'Sub Admin & Penanggung Jawab Inst. Pengendali (Kepala Urusan Instalasi Pengendali)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Inst. Pengendali (Kepala Urusan Instalasi Pengendali)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'tantypuspo.15@gmail.com'],
            [
                'name' => 'SUTANTI PUSPOSARI, S.Kep.Ns.',
                'nip' => '197910152006042027',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Pav. Anggrek',
                'penugasan' => 'Sub Admin & Penanggung Jawab Pav. Anggrek (Kepala Pavilyun Anggrek)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Pav. Anggrek (Kepala Pavilyun Anggrek)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'puje.anggayuni@gmail.com'],
            [
                'name' => 'PUJE ANGGAYUNI, S.Kep.Ns',
                'nip' => '198309212009022001',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Pav. Bougenville',
                'penugasan' => 'Sub Admin & Penanggung Jawab Pav. Bougenville (Kepala Pavilyun Bougenvile)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Pav. Bougenville (Kepala Pavilyun Bougenvile)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'adimasqur@gmail.com'],
            [
                'name' => 'MASKURNIADI, S.Kep.Ns',
                'nip' => '197410071997031001',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Pav. Dahlia',
                'penugasan' => 'Sub Admin & Penanggung Jawab Pav. Dahlia (Kepala Paviliun Dahlia)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Pav. Dahlia (Kepala Paviliun Dahlia)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'noenk612@gmail.com'],
            [
                'name' => 'SITI NURHASANAH, S.ST',
                'nip' => '197912062005012012',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Pav. Mawar',
                'penugasan' => 'Sub Admin & Penanggung Jawab Pav. Mawar (Kepala Pavilyun Mawar)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Pav. Mawar (Kepala Pavilyun Mawar)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'fettyfatkhiyah82@gmail.com'],
            [
                'name' => 'FETTY FATKHIYAH, S.ST.M.Si',
                'nip' => '197602042006042024',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Pav. Melati',
                'penugasan' => 'Sub Admin & Penanggung Jawab Pav. Melati (Kepala Pavilyun Melati)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Pav. Melati (Kepala Pavilyun Melati)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'ditosunardi@gmail.com'],
            [
                'name' => 'SUNARDI, S.Kep.Ns',
                'nip' => '196907051989031006',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Pav. Rengganis',
                'penugasan' => 'Sub Admin & Penanggung Jawab Pav. Rengganis (Kepala Pavilyun Rengganis)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Pav. Rengganis (Kepala Pavilyun Rengganis)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'rahayubondowoso@gmail.com'],
            [
                'name' => 'RAHAYU SRI WAHYUNI, S.Kep.Ns',
                'nip' => '197305051997032005',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Pav. Seruni',
                'penugasan' => 'Sub Admin & Penanggung Jawab Pav. Seruni (Kepala Pavilyun Seruni)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Pav. Seruni (Kepala Pavilyun Seruni)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'endangpurnawatiepi@gmail.com'],
            [
                'name' => 'ENDANG PURNAWATI, S.Kep.Ns',
                'nip' => '197806212006042023',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Pav. Teratai',
                'penugasan' => 'Sub Admin & Penanggung Jawab Pav. Teratai (Kepala Pavilyun Teratai)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Pav. Teratai (Kepala Pavilyun Teratai)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'madpii29@gmail.com'],
            [
                'name' => 'AHMAD SAFI I, S. Kep.Ns',
                'nip' => '197504231997031003',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Pav. Seroja',
                'penugasan' => 'Sub Admin & Penanggung Jawab Pav. Seroja (Kepala Pavilyun Seroja)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Pav. Seroja (Kepala Pavilyun Seroja)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'abisyakila@gmail.com'],
            [
                'name' => 'MOHAMMAD HENRI WAHYONO, S.Kep.Ns., M.Kes',
                'nip' => '198202032003121004',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Pav. Krisan',
                'penugasan' => 'Sub Admin & Penanggung Jawab Pav. Krisan (Kepala Pavilyun Krisan)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Pav. Krisan (Kepala Pavilyun Krisan)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'enymahija@gmail.com'],
            [
                'name' => 'ENY YULIATI, S.Kep, Ns.,M.Mkes',
                'nip' => '196907171991032014',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'PPI',
                'penugasan' => 'Sub Admin & Penanggung Jawab PPI (Kepala Unit PPI)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin PPI (Kepala Unit PPI)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'herijuniarto2@gmail.com'],
            [
                'name' => 'HERI JUNIARTO, S.Kep.Ns',
                'nip' => '197506122003121004',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Unit Endoskopi',
                'penugasan' => 'Sub Admin & Penanggung Jawab Unit Endoskopi (Kaur Unit Endoscopy)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Unit Endoskopi (Kaur Unit Endoscopy)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'syafiin99@gmail.com'],
            [
                'name' => 'SYAFIIN, S.Kep.Ns',
                'nip' => '197202161995031002',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Unit CATH LAB',
                'penugasan' => 'Sub Admin & Penanggung Jawab Unit CATH LAB (Kepala Ruang Unit Cathlab)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Unit CATH LAB (Kepala Ruang Unit Cathlab)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'dewitien70@gmail.com'],
            [
                'name' => 'TIEN SUHADEWI, S.Kep.Ns',
                'nip' => '197707022007012004',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Inst. Kemoterapi',
                'penugasan' => 'Sub Admin & Penanggung Jawab Inst. Kemoterapi (Kepala Pelayanan Instalasi Kemoterapi)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Inst. Kemoterapi (Kepala Pelayanan Instalasi Kemoterapi)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'asmono69@gmail.com'],
            [
                'name' => 'ASMONO, S.Kep.Ns',
                'nip' => '197003051996031005',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Inst. Anestesi',
                'penugasan' => 'Sub Admin & Penanggung Jawab Inst. Anestesi (Kepala Instalasi Anestesi)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Inst. Anestesi (Kepala Instalasi Anestesi)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'hestyhafi@gmail.com'],
            [
                'name' => 'drg HESTY TULUS PANGGIH ARINI, MMRS',
                'nip' => '197607022009032003',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Bidang Pelayanan Penunjang',
                'penugasan' => 'Sub Admin & Penanggung Jawab Bidang Pelayanan Penunjang (Kepala Bidang Pelayanan Penunjang)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Bidang Pelayanan Penunjang (Kepala Bidang Pelayanan Penunjang)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'ny.linda77@gmail.com'],
            [
                'name' => 'LINDA DWI ASTUTI, S.Kep.Ns',
                'nip' => '197705011997032003',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Inst. Rawat Jalan',
                'penugasan' => 'Sub Admin & Penanggung Jawab Inst. Rawat Jalan (Kepala Urusan Instalasi Rawat Jalan)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Inst. Rawat Jalan (Kepala Urusan Instalasi Rawat Jalan)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'sitidenik76@gmail.com'],
            [
                'name' => 'SITI DENIK MU AWANAH, SST',
                'nip' => '197603172005012008',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Unit Elektromedik IPSRS',
                'penugasan' => 'Sub Admin & Penanggung Jawab Unit Elektromedik IPSRS (Kepala Pelayanan Elektromedik IPSRS)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Unit Elektromedik IPSRS (Kepala Pelayanan Elektromedik IPSRS)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'bondowosohijrah@gmail.com'],
            [
                'name' => 'DIDIK KURNIYANTO, S.Kep.Ns., M.M',
                'nip' => '198112062008011011',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Bagian Tata Usaha, Informasi dan Pemasaran',
                'penugasan' => 'Sub Admin & Penanggung Jawab Bagian Tata Usaha, Informasi dan Pemasaran (Kepala Sie Tata Usaha, Informasi dan Pemasaran)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Bagian Tata Usaha, Informasi dan Pemasaran (Kepala Sie Tata Usaha, Informasi dan Pemasaran)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'intukwijayanti@gmail.com'],
            [
                'name' => 'INTUK WIJAYANTI, A.Md',
                'nip' => '197009141998032006',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Bagian Mobilisasi Dana',
                'penugasan' => 'Sub Admin & Penanggung Jawab Bagian Mobilisasi Dana (Kepala Sie Mobilisasi Dana)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Bagian Mobilisasi Dana (Kepala Sie Mobilisasi Dana)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'r_nasavalia@yahoo.co.id'],
            [
                'name' => 'ERNA HAYUNIATI INDRIANINGTYAS, SE',
                'nip' => '197709302009012001',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Bagian Perbendaharaan',
                'penugasan' => 'Sub Admin & Penanggung Jawab Bagian Perbendaharaan (Kepala Sie Perbendaharaan)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Bagian Perbendaharaan (Kepala Sie Perbendaharaan)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'ach.arfan@gmail.com'],
            [
                'name' => 'ACHMAD ARFANDI, S.M',
                'nip' => '197709102007011008',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Bagian Penyusunan Program dan Anggaran',
                'penugasan' => 'Sub Admin & Penanggung Jawab Bagian Penyusunan Program dan Anggaran (Kepala Sie Perencanaan dan Penyusunan Program)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Bagian Penyusunan Program dan Anggaran (Kepala Sie Perencanaan dan Penyusunan Program)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'yenydevitanti@gmail.com'],
            [
                'name' => 'YENY DEVITANTI, SE, M.Si',
                'nip' => '198001012009022007',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Bagian Monitoring dan Evaluasi',
                'penugasan' => 'Sub Admin & Penanggung Jawab Bagian Monitoring dan Evaluasi (Analis Kebijakan Ahli Muda | Anggota Tim Pembangunan Zona Integritas | Sekretaris Tim Reformasi Birokrasi | Sub.Koord. Monitoring dan Evaluasi)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Bagian Monitoring dan Evaluasi (Analis Kebijakan Ahli Muda | Anggota Tim Pembangunan Zona Integritas | Sekretaris Tim Reformasi Birokrasi | Sub.Koord. Monitoring dan Evaluasi)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'wahyunifauziah83@gmail.com'],
            [
                'name' => 'WAHYUNI FAUZIAH, S. Kep., Ns., MHS.,Ph.D',
                'nip' => '198306222007012006',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Lain - Lain',
                'penugasan' => 'Sub Admin & Penanggung Jawab Lain - Lain (Perawat)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Lain - Lain (Perawat)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'budieyanto207@gmail.com'],
            [
                'name' => 'BUDIYANTO',
                'nip' => '-',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Cleaning Service',
                'penugasan' => 'Sub Admin & Penanggung Jawab Cleaning Service (Kepala Cleaning Service)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Cleaning Service (Kepala Cleaning Service)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'shinta29bondowoso@gmail.com'],
            [
                'name' => 'YOYOK KURNIA MAHMUDI',
                'nip' => '198407212025211098',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Security',
                'penugasan' => 'Sub Admin & Penanggung Jawab Security (Kepala Security)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Security (Kepala Security)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'tayyibtayyib58@gmail.com'],
            [
                'name' => 'TAYYIB',
                'nip' => '197507102007011011',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Pengemudi',
                'penugasan' => 'Sub Admin & Penanggung Jawab Pengemudi (Kepala Pengemudi)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Pengemudi (Kepala Pengemudi)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'estupratikasari@gmail.com'],
            [
                'name' => 'ESTU PRATIKA SARI, S.ST',
                'nip' => '199409242023212002',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Front Office',
                'penugasan' => 'Sub Admin & Penanggung Jawab Front Office (Kepala Front Office)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Front Office (Kepala Front Office)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'attakhoir@gmail.com'],
            [
                'name' => 'M. AGUS HIDAYATULLAH, S.Kep.Ns',
                'nip' => '197508162000121003',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Pav. Camelia',
                'penugasan' => 'Sub Admin & Penanggung Jawab Pav. Camelia (Kepala Pav. Camelia)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Pav. Camelia (Kepala Pav. Camelia)',
            ]
        );
        User::updateOrCreate(
            ['email' => 'baiihaqiiahmad@gmail.com'],
            [
                'name' => 'AHMAD BAIHAQI',
                'nip' => '-',
                'password' => Hash::make('rsud123'),
                'role' => 'sub_admin',
                'unit' => 'Orientasi',
                'penugasan' => 'Sub Admin & Penanggung Jawab Orientasi (Dokter Umum / PPDS)',
                'status' => 'Aktif',
                'deskripsi' => 'Sub Admin Orientasi (Dokter Umum / PPDS)',
            ]
        );
    }
}
