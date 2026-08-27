<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UnitSeeder extends Seeder
{
    /**
     * Seed 55 Unit, Paviliun, & Instalasi Resmi RSUD Dr. H. Koesnandi
     */
    public function run(): void
    {
        $rawUnits = [
            ['kode' => 'UNIT-001', 'nama' => 'Direktur Dan Wadir', 'tipe' => 'Manajemen & Struktural', 'kepala' => 'dr. YUS PRIYATNA ADRYANTO. Sp.P, FISR', 'nip' => '197710022006041006', 'nik' => '3511080210770001', 'email' => 'Yuspriyatna@yahoo.co.id', 'jabatan' => 'Direktur RSUD Dr. H. Koesnandi'],
            ['kode' => 'UNIT-002', 'nama' => 'Bagian Kepegawaian', 'tipe' => 'Manajemen & Struktural', 'kepala' => 'ST ZUMAROH, SH.Msi', 'nip' => '197303011998092001', 'nik' => '3511114103730004', 'email' => 'stjumaroh73@gmail.com', 'jabatan' => 'Subkor. Kepegawaian & Pengembangan SDM'],
            ['kode' => 'UNIT-003', 'nama' => 'Bagian Keuangan', 'tipe' => 'Manajemen & Struktural', 'kepala' => 'ETSIE VERANINGSIH, SE, M.Si', 'nip' => '197602072002122001', 'nik' => '3511114702760002', 'email' => 'etsieveraningsih@gmail.com', 'jabatan' => 'Kepala Bagian Keuangan'],
            ['kode' => 'UNIT-004', 'nama' => 'Bidang Keperawatan', 'tipe' => 'Manajemen & Struktural', 'kepala' => 'YUNARSO, S.Kep.Ners', 'nip' => '196904121995031003', 'nik' => '3511111204690005', 'email' => 'yunarsooppo@gmail.com', 'jabatan' => 'Subkor. Pemeliharaan & Pengembangan Fasilitas Keperawatan'],
            ['kode' => 'UNIT-005', 'nama' => 'Bagian Perencanaan dan Pengembangan', 'tipe' => 'Manajemen & Struktural', 'kepala' => 'NURUL HIDAYAT, SKM., M.Kes', 'nip' => '197707052003121009', 'nik' => '3511080507770006', 'email' => 'hidayat92@yahoo.com', 'jabatan' => 'Kabag. Perencanaan & Penyusunan Program'],
            ['kode' => 'UNIT-006', 'nama' => 'Bagian Rumah Tangga & Inst Perbekalan', 'tipe' => 'Manajemen & Struktural', 'kepala' => 'BUDI HARTONO, S. Sos', 'nip' => '197602292008011010', 'nik' => '3511132902760001', 'email' => 'bd290276@gmail.com', 'jabatan' => 'Pengolah Pemanfaatan Barang Milik Daerah'],
            ['kode' => 'UNIT-007', 'nama' => 'Bagian Umum', 'tipe' => 'Manajemen & Struktural', 'kepala' => 'EKO BUDIANTO, SP. M.M.', 'nip' => '197711261999011001', 'nik' => '3511112611770001', 'email' => 'budiantoeko63@yahoo.co.id', 'jabatan' => 'Kepala Bagian Umum'],
            ['kode' => 'UNIT-008', 'nama' => 'Bidang Pelayanan Medik', 'tipe' => 'Manajemen & Struktural', 'kepala' => 'dr. SINTA AGITA ANGGRAINI', 'nip' => '198312162009022008', 'nik' => '3511115612830002', 'email' => 'dr.sintaanggraini@gmail.com', 'jabatan' => 'Kepala Bidang Pelayanan Medik'],
            ['kode' => 'UNIT-009', 'nama' => 'ICU', 'tipe' => 'Rawat Inap & Paviliun', 'kepala' => 'I\'ID ROSYIDAH, S.Kep.Ns', 'nip' => '197608061999032002', 'nik' => '3511134608760002', 'email' => 'iidrosyidah2015@gmail.com', 'jabatan' => 'Kepala Ruang ICU'],
            ['kode' => 'UNIT-010', 'nama' => 'IGD', 'tipe' => 'Pelayanan Kritis & Tindakan Medis', 'kepala' => 'dr. ADHI SUDARMADJI', 'nip' => '198410272009021003', 'nik' => '3511112710840004', 'email' => 'adhi_dr@yahoo.co.id', 'jabatan' => 'Kepala IGD'],
            ['kode' => 'UNIT-011', 'nama' => 'ICCU', 'tipe' => 'Rawat Inap & Paviliun', 'kepala' => 'HERI SISWANTO, S.Kep.Ns., M.Kep', 'nip' => '198009022005011005', 'nik' => '3511110209800003', 'email' => 'siswantoheri80@gmail.com', 'jabatan' => 'Kepala Ruang ICCU'],
            ['kode' => 'UNIT-012', 'nama' => 'Inst. Bedah Sentral', 'tipe' => 'Pelayanan Kritis & Tindakan Medis', 'kepala' => 'EDHI PURWANTO, S.Kep.Ns', 'nip' => '197305181995031002', 'nik' => '3511111805730005', 'email' => 'edhipurwanto73@gmil.com', 'jabatan' => 'Kepala Ruang IBS'],
            ['kode' => 'UNIT-013', 'nama' => 'Inst. CSSD & Loundry', 'tipe' => 'Penunjang Medis & Fasilitas', 'kepala' => 'SAIFUL WALID, S.Kep, Ns, M.MKes', 'nip' => '197001051996031004', 'nik' => '3512110501700004', 'email' => 'saifulwalid@gmail.com', 'jabatan' => 'Kepala Instalasi CSSD Laundry'],
            ['kode' => 'UNIT-014', 'nama' => 'Inst. Farmasi', 'tipe' => 'Penunjang Medis & Fasilitas', 'kepala' => 'INDRI HARDINI, S. Farm. Apt', 'nip' => '198610112015032002', 'nik' => '3509215110860003', 'email' => 'indri.hardini.apt@gmail.com', 'jabatan' => 'Kepala Instalasi Farmasi'],
            ['kode' => 'UNIT-015', 'nama' => 'Inst. Gizi', 'tipe' => 'Penunjang Medis & Fasilitas', 'kepala' => 'FITRIA NUR RAHMI, S.Gz', 'nip' => '198008092006042022', 'nik' => '3511114908800001', 'email' => 'fitrianurrahmi@gmail.com', 'jabatan' => 'Kepala Instalasi Gizi'],
            ['kode' => 'UNIT-016', 'nama' => 'Inst. IPS RS', 'tipe' => 'Penunjang Medis & Fasilitas', 'kepala' => 'DANI PRIANTO, ST', 'nip' => '198310152006041010', 'nik' => '3511111510830002', 'email' => 'priantodani17@gmail.com', 'jabatan' => 'Pengelola Penataan Sarana dan Prasarana'],
            ['kode' => 'UNIT-017', 'nama' => 'Inst. Laboratorium', 'tipe' => 'Penunjang Medis & Fasilitas', 'kepala' => 'AGUS PRASETIYO, S.Si,M.Mkes', 'nip' => '197407232002121003', 'nik' => '3511102307740001', 'email' => 'agusprasetiyo9@gmail.com', 'jabatan' => 'Kepala Ruang Instalasi Laboratorium'],
            ['kode' => 'UNIT-018', 'nama' => 'Inst. Radiologi', 'tipe' => 'Penunjang Medis & Fasilitas', 'kepala' => 'HARTONO ZUPRIADY, S.Tr.Kes', 'nip' => '197805052003121005', 'nik' => '3511140505780003', 'email' => 'Hartono5194@gmail.com', 'jabatan' => 'Kaur Pelayanan Instalasi Radiologi'],
            ['kode' => 'UNIT-019', 'nama' => 'Inst. Rawat Jenazah', 'tipe' => 'Penunjang Medis & Fasilitas', 'kepala' => 'ISMANTO, A.Md.Kep', 'nip' => '198901012025211300', 'nik' => '3511110101890034', 'email' => 'edyotnamsi@gmail.com', 'jabatan' => 'Kaur Pelayanan Rawat Jenazah'],
            ['kode' => 'UNIT-020', 'nama' => 'Inst. Rekam Medik', 'tipe' => 'Penunjang Medis & Fasilitas', 'kepala' => 'PRASTIWI, A.Md', 'nip' => '199207272015032008', 'nik' => '3510096707920002', 'email' => 'tiwipras59@yahoo.co.id', 'jabatan' => 'Kepala Instalasi Rekam Medik'],
            ['kode' => 'UNIT-021', 'nama' => 'Inst. Hemodialisa', 'tipe' => 'Pelayanan Kritis & Tindakan Medis', 'kepala' => 'SYAIFUL ANWAR, S.Kep.Ns', 'nip' => '197901172003121004', 'nik' => '3511111701790006', 'email' => 'sy41ful17@gmail.com', 'jabatan' => 'Kaur Instalasi Hemodialisa'],
            ['kode' => 'UNIT-022', 'nama' => 'Inst. Informasi Teknologi ( IT )', 'tipe' => 'Pelayanan Umum & Operasional', 'kepala' => 'FIQIH WAHYUDIANSYAH, S.Kom', 'nip' => '199301282019031002', 'nik' => '3511152801930001', 'email' => 'fiqihwahyudiansyah@gmail.com', 'jabatan' => 'Kepala Instalasi IT'],
            ['kode' => 'UNIT-023', 'nama' => 'Inst. PKRS', 'tipe' => 'Manajemen & Struktural', 'kepala' => 'Nur `Aini Lestari, S.KM', 'nip' => '198806102022042001', 'nik' => '3511115006880004', 'email' => 'ainynunik@gmail.com', 'jabatan' => 'Kepala Instalasi PKRS'],
            ['kode' => 'UNIT-024', 'nama' => 'Inst. Sanitasi', 'tipe' => 'Penunjang Medis & Fasilitas', 'kepala' => 'AGUS JULIANTO, S.KL', 'nip' => '197907182006041014', 'nik' => '3511111807790005', 'email' => 'agusjulianto79@gmail.com', 'jabatan' => 'Kepala Instalasi Sanitasi'],
            ['kode' => 'UNIT-025', 'nama' => 'Inst. Pengendali', 'tipe' => 'Manajemen & Struktural', 'kepala' => 'SITI KHOTIMAH', 'nip' => '197004112007012009', 'nik' => '3511115104700002', 'email' => 'sitikhotimahrsu@gmail.com', 'jabatan' => 'Kepala Urusan Instalasi Pengendali'],
            ['kode' => 'UNIT-026', 'nama' => 'Pav. Anggrek', 'tipe' => 'Rawat Inap & Paviliun', 'kepala' => 'SUTANTI PUSPOSARI, S.Kep.Ns.', 'nip' => '197910152006042027', 'nik' => '3511135510790002', 'email' => 'tantypuspo.15@gmail.com', 'jabatan' => 'Kepala Pavilyun Anggrek'],
            ['kode' => 'UNIT-027', 'nama' => 'Pav. Bougenville', 'tipe' => 'Rawat Inap & Paviliun', 'kepala' => 'PUJE ANGGAYUNI, S.Kep.Ns', 'nip' => '198309212009022001', 'nik' => '3511116109830002', 'email' => 'puje.anggayuni@gmail.com', 'jabatan' => 'Kepala Pavilyun Bougenvile'],
            ['kode' => 'UNIT-028', 'nama' => 'Pav. Dahlia', 'tipe' => 'Rawat Inap & Paviliun', 'kepala' => 'MASKURNIADI, S.Kep.Ns', 'nip' => '197410071997031001', 'nik' => '3511110710740004', 'email' => 'adimasqur@gmail.com', 'jabatan' => 'Kepala Paviliun Dahlia'],
            ['kode' => 'UNIT-029', 'nama' => 'Pav. Mawar', 'tipe' => 'Rawat Inap & Paviliun', 'kepala' => 'SITI NURHASANAH, S.ST', 'nip' => '197912062005012012', 'nik' => '3511114612790002', 'email' => 'noenk612@gmail.com', 'jabatan' => 'Kepala Pavilyun Mawar'],
            ['kode' => 'UNIT-030', 'nama' => 'Pav. Melati', 'tipe' => 'Rawat Inap & Paviliun', 'kepala' => 'FETTY FATKHIYAH, S.ST.M.Si', 'nip' => '197602042006042024', 'nik' => '3509204402760002', 'email' => 'fettyfatkhiyah82@gmail.com', 'jabatan' => 'Kepala Pavilyun Melati'],
            ['kode' => 'UNIT-031', 'nama' => 'Pav. Rengganis', 'tipe' => 'Rawat Inap & Paviliun', 'kepala' => 'SUNARDI, S.Kep.Ns', 'nip' => '196907051989031006', 'nik' => '3511020507690001', 'email' => 'ditosunardi@gmail.com', 'jabatan' => 'Kepala Pavilyun Rengganis'],
            ['kode' => 'UNIT-032', 'nama' => 'Pav. Seruni', 'tipe' => 'Rawat Inap & Paviliun', 'kepala' => 'RAHAYU SRI WAHYUNI, S.Kep.Ns', 'nip' => '197305051997032005', 'nik' => '3511114505730010', 'email' => 'rahayubondowoso@gmail.com', 'jabatan' => 'Kepala Pavilyun Seruni'],
            ['kode' => 'UNIT-033', 'nama' => 'Pav. Teratai', 'tipe' => 'Rawat Inap & Paviliun', 'kepala' => 'ENDANG PURNAWATI, S.Kep.Ns', 'nip' => '197806212006042023', 'nik' => '3511136106780003', 'email' => 'endangpurnawatiepi@gmail.com', 'jabatan' => 'Kepala Pavilyun Teratai'],
            ['kode' => 'UNIT-034', 'nama' => 'Pav. Seroja', 'tipe' => 'Rawat Inap & Paviliun', 'kepala' => 'AHMAD SAFI I, S. Kep.Ns', 'nip' => '197504231997031003', 'nik' => '3511112304750004', 'email' => 'madpii29@gmail.com', 'jabatan' => 'Kepala Pavilyun Seroja'],
            ['kode' => 'UNIT-035', 'nama' => 'Pav. Krisan', 'tipe' => 'Rawat Inap & Paviliun', 'kepala' => 'MOHAMMAD HENRI WAHYONO, S.Kep.Ns., M.Kes', 'nip' => '198202032003121004', 'nik' => '3509160302820001', 'email' => 'abisyakila@gmail.com', 'jabatan' => 'Kepala Pavilyun Krisan'],
            ['kode' => 'UNIT-036', 'nama' => 'PPI', 'tipe' => 'Manajemen & Struktural', 'kepala' => 'ENY YULIATI, S.Kep, Ns.,M.Mkes', 'nip' => '196907171991032014', 'nik' => '3511115707690002', 'email' => 'enymahija@gmail.com', 'jabatan' => 'Kepala Unit PPI'],
            ['kode' => 'UNIT-037', 'nama' => 'Unit Endoskopi', 'tipe' => 'Pelayanan Kritis & Tindakan Medis', 'kepala' => 'HERI JUNIARTO, S.Kep.Ns', 'nip' => '197506122003121004', 'nik' => '3511081206750005', 'email' => 'herijuniarto2@gmail.com', 'jabatan' => 'Kaur Unit Endoscopy'],
            ['kode' => 'UNIT-038', 'nama' => 'Unit CATH LAB', 'tipe' => 'Pelayanan Kritis & Tindakan Medis', 'kepala' => 'SYAFIIN, S.Kep.Ns', 'nip' => '197202161995031002', 'nik' => '3511081602720002', 'email' => 'syafiin99@gmail.com', 'jabatan' => 'Kepala Ruang Unit Cathlab'],
            ['kode' => 'UNIT-039', 'nama' => 'Inst. Kemoterapi', 'tipe' => 'Pelayanan Kritis & Tindakan Medis', 'kepala' => 'TIEN SUHADEWI, S.Kep.Ns', 'nip' => '197707022007012004', 'nik' => '3511114207770001', 'email' => 'dewitien70@gmail.com', 'jabatan' => 'Kepala Pelayanan Instalasi Kemoterapi'],
            ['kode' => 'UNIT-040', 'nama' => 'Inst. Anestesi', 'tipe' => 'Pelayanan Kritis & Tindakan Medis', 'kepala' => 'ASMONO, S.Kep.Ns', 'nip' => '197003051996031005', 'nik' => '3511110503700002', 'email' => 'asmono69@gmail.com', 'jabatan' => 'Kepala Instalasi Anestesi'],
            ['kode' => 'UNIT-041', 'nama' => 'Bidang Pelayanan Penunjang', 'tipe' => 'Manajemen & Struktural', 'kepala' => 'drg HESTY TULUS PANGGIH ARINI, MMRS', 'nip' => '197607022009032003', 'nik' => '3509214207760001', 'email' => 'hestyhafi@gmail.com', 'jabatan' => 'Kepala Bidang Pelayanan Penunjang'],
            ['kode' => 'UNIT-042', 'nama' => 'Inst. Rawat Jalan', 'tipe' => 'Pelayanan Umum & Operasional', 'kepala' => 'LINDA DWI ASTUTI, S.Kep.Ns', 'nip' => '197705011997032003', 'nik' => '3511114105770001', 'email' => 'ny.linda77@gmail.com', 'jabatan' => 'Kepala Urusan Instalasi Rawat Jalan'],
            ['kode' => 'UNIT-043', 'nama' => 'Unit Elektromedik IPSRS', 'tipe' => 'Penunjang Medis & Fasilitas', 'kepala' => 'SITI DENIK MU AWANAH, SST', 'nip' => '197603172005012008', 'nik' => '3511115703760002', 'email' => 'sitidenik76@gmail.com', 'jabatan' => 'Kepala Pelayanan Elektromedik IPSRS'],
            ['kode' => 'UNIT-044', 'nama' => 'Bagian Tata Usaha, Informasi dan Pemasaran', 'tipe' => 'Manajemen & Struktural', 'kepala' => 'DIDIK KURNIYANTO, S.Kep.Ns., M.M', 'nip' => '198112062008011011', 'nik' => '3511050612810002', 'email' => 'bondowosohijrah@gmail.com', 'jabatan' => 'Kepala Sie Tata Usaha, Informasi dan Pemasaran'],
            ['kode' => 'UNIT-045', 'nama' => 'Bagian Mobilisasi Dana', 'tipe' => 'Manajemen & Struktural', 'kepala' => 'INTUK WIJAYANTI, A.Md', 'nip' => '197009141998032006', 'nik' => '3511125409700002', 'email' => 'intukwijayanti@gmail.com', 'jabatan' => 'Kepala Sie Mobilisasi Dana'],
            ['kode' => 'UNIT-046', 'nama' => 'Bagian Perbendaharaan', 'tipe' => 'Manajemen & Struktural', 'kepala' => 'ERNA HAYUNIATI INDRIANINGTYAS, SE', 'nip' => '197709302009012001', 'nik' => '3511057009770001', 'email' => 'r_nasavalia@yahoo.co.id', 'jabatan' => 'Kepala Sie Perbendaharaan'],
            ['kode' => 'UNIT-047', 'nama' => 'Bagian Penyusunan Program dan Anggaran', 'tipe' => 'Manajemen & Struktural', 'kepala' => 'ACHMAD ARFANDI, S.M', 'nip' => '197709102007011008', 'nik' => '3511111009770004', 'email' => 'ach.arfan@gmail.com', 'jabatan' => 'Kepala Sie Perencanaan dan Penyusunan Program'],
            ['kode' => 'UNIT-048', 'nama' => 'Bagian Monitoring dan Evaluasi', 'tipe' => 'Manajemen & Struktural', 'kepala' => 'YENY DEVITANTI, SE, M.Si', 'nip' => '198001012009022007', 'nik' => '3511084101800006', 'email' => 'yenydevitanti@gmail.com', 'jabatan' => 'Sub.Koord. Monitoring dan Evaluasi'],
            ['kode' => 'UNIT-049', 'nama' => 'Lain - Lain', 'tipe' => 'Pelayanan Umum & Operasional', 'kepala' => 'WAHYUNI FAUZIAH, S. Kep., Ns., MHS.,Ph.D', 'nip' => '198306222007012006', 'nik' => '3509276206830004', 'email' => 'wahyunifauziah83@gmail.com', 'jabatan' => 'Perawat'],
            ['kode' => 'UNIT-050', 'nama' => 'Cleaning Service', 'tipe' => 'Pelayanan Umum & Operasional', 'kepala' => 'BUDIYANTO', 'nip' => '-', 'nik' => '3511081806950003', 'email' => 'budieyanto207@gmail.com', 'jabatan' => 'Kepala Cleaning Service'],
            ['kode' => 'UNIT-051', 'nama' => 'Security', 'tipe' => 'Pelayanan Umum & Operasional', 'kepala' => 'YOYOK KURNIA MAHMUDI', 'nip' => '198407212025211098', 'nik' => '3511012107840001', 'email' => 'shinta29bondowoso@gmail.com', 'jabatan' => 'Kepala Security'],
            ['kode' => 'UNIT-052', 'nama' => 'Pengemudi', 'tipe' => 'Pelayanan Umum & Operasional', 'kepala' => 'TAYYIB', 'nip' => '197507102007011011', 'nik' => '3511131007750001', 'email' => 'tayyibtayyib58@gmail.com', 'jabatan' => 'Kepala Pengemudi'],
            ['kode' => 'UNIT-053', 'nama' => 'Front Office', 'tipe' => 'Pelayanan Umum & Operasional', 'kepala' => 'ESTU PRATIKA SARI, S.ST', 'nip' => '199409242023212002', 'nik' => '3509056409940001', 'email' => 'estupratikasari@gmail.com', 'jabatan' => 'Kepala Front Office'],
            ['kode' => 'UNIT-054', 'nama' => 'Pav. Camelia', 'tipe' => 'Rawat Inap & Paviliun', 'kepala' => 'M. AGUS HIDAYATULLAH, S.Kep.Ns', 'nip' => '197508162000121003', 'nik' => '3511111608750009', 'email' => 'attakhoir@gmail.com', 'jabatan' => 'Kepala Pav. Camelia'],
            ['kode' => 'UNIT-055', 'nama' => 'Orientasi', 'tipe' => 'Pelayanan Umum & Operasional', 'kepala' => 'AHMAD BAIHAQI', 'nip' => '-', 'nik' => '3522121108960003', 'email' => 'baiihaqiiahmad@gmail.com', 'jabatan' => 'Dokter Umum / PPDS'],
        ];

        foreach ($rawUnits as $item) {
            // 1. Simpan atau perbarui data Unit (tabel units)
            $unit = Unit::updateOrCreate(
                ['kode_unit' => $item['kode']],
                [
                    'nama' => $item['nama'],
                    'tipe' => $item['tipe'],
                    'kepala' => $item['kepala'],
                    'nip' => $item['nip'],
                    'email' => $item['email'],
                    'id_aset' => [],
                    'total_aset' => 0,
                    'total_nilai' => 'Rp 0',
                ]
            );

            // 2. Simpan atau perbarui Akun User (Admin khusus untuk Bagian Rumah Tangga & Inst Perbekalan)
            $isRumahTangga = str_contains(strtolower($item['nama']), 'rumah tangga') || str_contains(strtolower($item['nama']), 'perbekalan');
            User::updateOrCreate(
                ['email' => $item['email']],
                [
                    'name' => $item['kepala'],
                    'role' => $isRumahTangga ? 'admin' : 'sub_admin',
                    'unit_id' => $unit->id,
                    'penugasan' => $isRumahTangga ? 'Admin Pencatatan Aset & Rumah Tangga' : ('Sub Admin Ruangan ' . $item['nama']),
                    'status' => 'Aktif',
                    'password' => Hash::make('rsud123'),
                    'deskripsi' => $isRumahTangga ? 'Akun Admin Resmi Pencatatan Barang & Rumah Tangga' : ('Akun Sub Admin Otomatis dari Pendaftaran Unit ' . $item['nama']),
                ]
            );
        }
    }
}
