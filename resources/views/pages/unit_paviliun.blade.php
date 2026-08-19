<x-layout title="Unit & Paviliun - SIMAT-RK">
    @section('page-title', 'Unit & Paviliun')
    @section('breadcrumb', 'Master Utama / Unit & Paviliun')

    <div x-data="{
        searchQuery: '',
        viewMode: 'grid', // 'grid' or 'table'
        showAddModal: false,
        showEditModal: false,
        showDetailModal: false,
        showPrintKIRModal: false,
        showEditKIRForm: false,
        selectedUnit: null,

        // Data Dokumen Cetak KIR
        kirDoc: {
            nomor_surat: '028/KIR-RSUD/2026',
            tanggal_pengesahan: '10 Januari 2026',
            pj_nama: '',
            pj_nip: '19880512 201201 2 004',
            pj_jabatan: 'Kepala / Penanggung Jawab Ruangan',
            pengurus_nama: 'BAMBANG HERMANTO, S.Sos',
            pengurus_nip: '19790315 200801 1 012',
            pengurus_jabatan: 'Pengurus Barang Pengelola Aset',
            direktur_nama: 'dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR.',
            direktur_nip: '19771002 200604 1 006'
        },

        // Filter di dalam Modal Detail Aset Ruangan
        detailSearchQuery: '',
        detailKondisiFilter: 'all',
        detailCategoryFilter: 'all',

        units: [
            {
                id: 1,
                kode: 'UNIT-001',
                nama: 'Direktur Dan Wadir',
                tipe: 'Manajemen & Struktural',
                kepala: 'dr. YUS PRIYATNA ADRYANTO. Sp.P, FISR',
                pj_aset: 'dr. YUS PRIYATNA ADRYANTO. Sp.P, FISR',
                nip: '197710022006041006',
                nik: '3511080210770001',
                email: 'Yuspriyatna@yahoo.co.id',
                jabatan_pj: 'Direktur RSUD Dr. H. Koesnandi',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 2,
                kode: 'UNIT-002',
                nama: 'Bagian Kepegawaian',
                tipe: 'Manajemen & Struktural',
                kepala: 'ST ZUMAROH, SH.Msi',
                pj_aset: 'ST ZUMAROH, SH.Msi',
                nip: '197303011998092001',
                nik: '3511114103730004',
                email: 'stjumaroh73@gmail.com',
                jabatan_pj: 'Subkor. Kepegawaian & Pengembangan SDM',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 3,
                kode: 'UNIT-003',
                nama: 'Bagian Keuangan',
                tipe: 'Manajemen & Struktural',
                kepala: 'ETSIE VERANINGSIH, SE, M.Si',
                pj_aset: 'ETSIE VERANINGSIH, SE, M.Si',
                nip: '197602072002122001',
                nik: '3511114702760002',
                email: 'etsieveraningsih@gmail.com',
                jabatan_pj: 'Kepala Bagian Keuangan',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 4,
                kode: 'UNIT-004',
                nama: 'Bidang Keperawatan',
                tipe: 'Manajemen & Struktural',
                kepala: 'YUNARSO, S.Kep.Ners',
                pj_aset: 'YUNARSO, S.Kep.Ners',
                nip: '196904121995031003',
                nik: '3511111204690005',
                email: 'yunarsooppo@gmail.com',
                jabatan_pj: 'Subkor. Pemeliharaan & Pengembangan Fasilitas Keperawatan',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 5,
                kode: 'UNIT-005',
                nama: 'Bagian Perencanaan dan Pengembangan',
                tipe: 'Manajemen & Struktural',
                kepala: 'NURUL HIDAYAT, SKM., M.Kes',
                pj_aset: 'NURUL HIDAYAT, SKM., M.Kes',
                nip: '197707052003121009',
                nik: '3511080507770006',
                email: 'hidayat92@yahoo.com',
                jabatan_pj: 'Analis Kebijakan Ahli Muda | Anggota Sub Komite Keselamatan dan Keamanan Komite Keselamatan dan Kesehatan Kerja | Kabag. Perencanaan & Penyusunan Program',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 6,
                kode: 'UNIT-006',
                nama: 'Bagian Rumah Tangga & Inst Perbekalan',
                tipe: 'Manajemen & Struktural',
                kepala: 'BUDI HARTONO, S. Sos',
                pj_aset: 'BUDI HARTONO, S. Sos',
                nip: '197602292008011010',
                nik: '3511132902760001',
                email: 'bd290276@gmail.com',
                jabatan_pj: 'Anggota Satuan Pengawas Internal | Anggota Sub Komite Penanggulangan Bencana (Emengency) Komite Keselamatan dan Kesehatan Kerja | Pengolah Pemanfaatan Barang Milik Daerah',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 7,
                kode: 'UNIT-007',
                nama: 'Bagian Umum',
                tipe: 'Manajemen & Struktural',
                kepala: 'EKO BUDIANTO, SP. M.M.',
                pj_aset: 'EKO BUDIANTO, SP. M.M.',
                nip: '197711261999011001',
                nik: '3511112611770001',
                email: 'budiantoeko63@yahoo.co.id',
                jabatan_pj: 'Kepala Bagian Umum',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 8,
                kode: 'UNIT-008',
                nama: 'Bidang Pelayanan Medik',
                tipe: 'Manajemen & Struktural',
                kepala: 'dr. SINTA AGITA ANGGRAINI',
                pj_aset: 'dr. SINTA AGITA ANGGRAINI',
                nip: '198312162009022008',
                nik: '3511115612830002',
                email: 'dr.sintaanggraini@gmail.com',
                jabatan_pj: 'Kepala Bidang Pelayanan Medik',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 9,
                kode: 'UNIT-009',
                nama: 'ICU',
                tipe: 'Rawat Inap & Paviliun',
                kepala: 'I\'ID ROSYIDAH, S.Kep.Ns',
                pj_aset: 'I\'ID ROSYIDAH, S.Kep.Ns',
                nip: '197608061999032002',
                nik: '3511134608760002',
                email: 'iidrosyidah2015@gmail.com',
                jabatan_pj: 'Kepala Ruang ICU',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 10,
                kode: 'UNIT-010',
                nama: 'IGD',
                tipe: 'Pelayanan Kritis & Tindakan Medis',
                kepala: 'dr. ADHI SUDARMADJI',
                pj_aset: 'dr. ADHI SUDARMADJI',
                nip: '198410272009021003',
                nik: '3511112710840004',
                email: 'adhi_dr@yahoo.co.id',
                jabatan_pj: 'Kepala IGD',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 11,
                kode: 'UNIT-011',
                nama: 'ICCU',
                tipe: 'Rawat Inap & Paviliun',
                kepala: 'HERI SISWANTO, S.Kep.Ns., M.Kep',
                pj_aset: 'HERI SISWANTO, S.Kep.Ns., M.Kep',
                nip: '198009022005011005',
                nik: '3511110209800003',
                email: 'siswantoheri80@gmail.com',
                jabatan_pj: 'Kepala Ruang ICCU',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 12,
                kode: 'UNIT-012',
                nama: 'Inst. Bedah Sentral',
                tipe: 'Pelayanan Kritis & Tindakan Medis',
                kepala: 'EDHI PURWANTO, S.Kep.Ns',
                pj_aset: 'EDHI PURWANTO, S.Kep.Ns',
                nip: '197305181995031002',
                nik: '3511111805730005',
                email: 'edhipurwanto73@gmil.com',
                jabatan_pj: 'Kepala Ruang IBS',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 13,
                kode: 'UNIT-013',
                nama: 'Inst. CSSD & Loundry',
                tipe: 'Penunjang Medis & Fasilitas',
                kepala: 'SAIFUL WALID, S.Kep, Ns, M.MKes',
                pj_aset: 'SAIFUL WALID, S.Kep, Ns, M.MKes',
                nip: '197001051996031004',
                nik: '3512110501700004',
                email: 'saifulwalid@gmail.com',
                jabatan_pj: 'Kepala Instalasi CSSD Laundry',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 14,
                kode: 'UNIT-014',
                nama: 'Inst. Farmasi',
                tipe: 'Penunjang Medis & Fasilitas',
                kepala: 'INDRI HARDINI, S. Farm. Apt',
                pj_aset: 'INDRI HARDINI, S. Farm. Apt',
                nip: '198610112015032002',
                nik: '3509215110860003',
                email: 'indri.hardini.apt@gmail.com',
                jabatan_pj: 'Kepala Instalasi Farmasi',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 15,
                kode: 'UNIT-015',
                nama: 'Inst. Gizi',
                tipe: 'Penunjang Medis & Fasilitas',
                kepala: 'FITRIA NUR RAHMI, S.Gz',
                pj_aset: 'FITRIA NUR RAHMI, S.Gz',
                nip: '198008092006042022',
                nik: '3511114908800001',
                email: 'fitrianurrahmi@gmail.com',
                jabatan_pj: 'Kepala Instalasi Gizi',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 16,
                kode: 'UNIT-016',
                nama: 'Inst. IPS RS',
                tipe: 'Penunjang Medis & Fasilitas',
                kepala: 'DANI PRIANTO, ST',
                pj_aset: 'DANI PRIANTO, ST',
                nip: '198310152006041010',
                nik: '3511111510830002',
                email: 'priantodani17@gmail.com',
                jabatan_pj: 'Anggota Sub Komite Sistem Penunjang (Utilitas) Komite Keselamatan dan Kesehatan Kerja | Pengelola Penataan Sarana dan Prasarana',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 17,
                kode: 'UNIT-017',
                nama: 'Inst. Laboratorium',
                tipe: 'Penunjang Medis & Fasilitas',
                kepala: 'AGUS PRASETIYO, S.Si,M.Mkes',
                pj_aset: 'AGUS PRASETIYO, S.Si,M.Mkes',
                nip: '197407232002121003',
                nik: '3511102307740001',
                email: 'agusprasetiyo9@gmail.com',
                jabatan_pj: 'Kepala Ruang Instalasi Laboratorium',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 18,
                kode: 'UNIT-018',
                nama: 'Inst. Radiologi',
                tipe: 'Penunjang Medis & Fasilitas',
                kepala: 'HARTONO ZUPRIADY, S.Tr.Kes',
                pj_aset: 'HARTONO ZUPRIADY, S.Tr.Kes',
                nip: '197805052003121005',
                nik: '3511140505780003',
                email: 'Hartono5194@gmail.com',
                jabatan_pj: 'Kaur Pelayanan Instalasi Radiologi',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 19,
                kode: 'UNIT-019',
                nama: 'Inst. Rawat Jenazah',
                tipe: 'Penunjang Medis & Fasilitas',
                kepala: 'ISMANTO, A.Md.Kep',
                pj_aset: 'ISMANTO, A.Md.Kep',
                nip: '198901012025211300',
                nik: '3511110101890034',
                email: 'edyotnamsi@gmail.com',
                jabatan_pj: 'Kaur Pelayanan Rawat Jenazah',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 20,
                kode: 'UNIT-020',
                nama: 'Inst. Rekam Medik',
                tipe: 'Penunjang Medis & Fasilitas',
                kepala: 'PRASTIWI, A.Md',
                pj_aset: 'PRASTIWI, A.Md',
                nip: '199207272015032008',
                nik: '3510096707920002',
                email: 'tiwipras59@yahoo.co.id',
                jabatan_pj: 'Kepala Instalasi Rekam Medik',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 21,
                kode: 'UNIT-021',
                nama: 'Inst. Hemodialisa',
                tipe: 'Pelayanan Kritis & Tindakan Medis',
                kepala: 'SYAIFUL ANWAR, S.Kep.Ns',
                pj_aset: 'SYAIFUL ANWAR, S.Kep.Ns',
                nip: '197901172003121004',
                nik: '3511111701790006',
                email: 'sy41ful17@gmail.com',
                jabatan_pj: 'Kaur Instalasi Hemodialisa',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 22,
                kode: 'UNIT-022',
                nama: 'Inst. Informasi Teknologi ( IT )',
                tipe: 'Pelayanan Umum & Operasional',
                kepala: 'FIQIH WAHYUDIANSYAH, S.Kom',
                pj_aset: 'FIQIH WAHYUDIANSYAH, S.Kom',
                nip: '199301282019031002',
                nik: '3511152801930001',
                email: 'fiqihwahyudiansyah@gmail.com',
                jabatan_pj: 'Kepala Instalasi IT',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 23,
                kode: 'UNIT-023',
                nama: 'Inst. PKRS',
                tipe: 'Manajemen & Struktural',
                kepala: 'Nur `Aini Lestari, S.KM',
                pj_aset: 'Nur `Aini Lestari, S.KM',
                nip: '198806102022042001',
                nik: '3511115006880004',
                email: 'ainynunik@gmail.com',
                jabatan_pj: 'Kepala Instalasi PKRS',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 24,
                kode: 'UNIT-024',
                nama: 'Inst. Sanitasi',
                tipe: 'Penunjang Medis & Fasilitas',
                kepala: 'AGUS JULIANTO, S.KL',
                pj_aset: 'AGUS JULIANTO, S.KL',
                nip: '197907182006041014',
                nik: '3511111807790005',
                email: 'agusjulianto79@gmail.com',
                jabatan_pj: 'Kepala Instalasi Sanitasi',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 25,
                kode: 'UNIT-025',
                nama: 'Inst. Pengendali',
                tipe: 'Manajemen & Struktural',
                kepala: 'SITI KHOTIMAH',
                pj_aset: 'SITI KHOTIMAH',
                nip: '197004112007012009',
                nik: '3511115104700002',
                email: 'sitikhotimahrsu@gmail.com',
                jabatan_pj: 'Kepala Urusan Instalasi Pengendali',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 26,
                kode: 'UNIT-026',
                nama: 'Pav. Anggrek',
                tipe: 'Rawat Inap & Paviliun',
                kepala: 'SUTANTI PUSPOSARI, S.Kep.Ns.',
                pj_aset: 'SUTANTI PUSPOSARI, S.Kep.Ns.',
                nip: '197910152006042027',
                nik: '3511135510790002',
                email: 'tantypuspo.15@gmail.com',
                jabatan_pj: 'Kepala Pavilyun Anggrek',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 27,
                kode: 'UNIT-027',
                nama: 'Pav. Bougenville',
                tipe: 'Rawat Inap & Paviliun',
                kepala: 'PUJE ANGGAYUNI, S.Kep.Ns',
                pj_aset: 'PUJE ANGGAYUNI, S.Kep.Ns',
                nip: '198309212009022001',
                nik: '3511116109830002',
                email: 'puje.anggayuni@gmail.com',
                jabatan_pj: 'Kepala Pavilyun Bougenvile',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 28,
                kode: 'UNIT-028',
                nama: 'Pav. Dahlia',
                tipe: 'Rawat Inap & Paviliun',
                kepala: 'MASKURNIADI, S.Kep.Ns',
                pj_aset: 'MASKURNIADI, S.Kep.Ns',
                nip: '197410071997031001',
                nik: '3511110710740004',
                email: 'adimasqur@gmail.com',
                jabatan_pj: 'Kepala Paviliun Dahlia',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 29,
                kode: 'UNIT-029',
                nama: 'Pav. Mawar',
                tipe: 'Rawat Inap & Paviliun',
                kepala: 'SITI NURHASANAH, S.ST',
                pj_aset: 'SITI NURHASANAH, S.ST',
                nip: '197912062005012012',
                nik: '3511114612790002',
                email: 'noenk612@gmail.com',
                jabatan_pj: 'Kepala Pavilyun Mawar',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 30,
                kode: 'UNIT-030',
                nama: 'Pav. Melati',
                tipe: 'Rawat Inap & Paviliun',
                kepala: 'FETTY FATKHIYAH, S.ST.M.Si',
                pj_aset: 'FETTY FATKHIYAH, S.ST.M.Si',
                nip: '197602042006042024',
                nik: '3509204402760002',
                email: 'fettyfatkhiyah82@gmail.com',
                jabatan_pj: 'Kepala Pavilyun Melati',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 31,
                kode: 'UNIT-031',
                nama: 'Pav. Rengganis',
                tipe: 'Rawat Inap & Paviliun',
                kepala: 'SUNARDI, S.Kep.Ns',
                pj_aset: 'SUNARDI, S.Kep.Ns',
                nip: '196907051989031006',
                nik: '3511020507690001',
                email: 'ditosunardi@gmail.com',
                jabatan_pj: 'Kepala Pavilyun Rengganis',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 32,
                kode: 'UNIT-032',
                nama: 'Pav. Seruni',
                tipe: 'Rawat Inap & Paviliun',
                kepala: 'RAHAYU SRI WAHYUNI, S.Kep.Ns',
                pj_aset: 'RAHAYU SRI WAHYUNI, S.Kep.Ns',
                nip: '197305051997032005',
                nik: '3511114505730010',
                email: 'rahayubondowoso@gmail.com',
                jabatan_pj: 'Kepala Pavilyun Seruni',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 33,
                kode: 'UNIT-033',
                nama: 'Pav. Teratai',
                tipe: 'Rawat Inap & Paviliun',
                kepala: 'ENDANG PURNAWATI, S.Kep.Ns',
                pj_aset: 'ENDANG PURNAWATI, S.Kep.Ns',
                nip: '197806212006042023',
                nik: '3511136106780003',
                email: 'endangpurnawatiepi@gmail.com',
                jabatan_pj: 'Kepala Pavilyun Teratai',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 34,
                kode: 'UNIT-034',
                nama: 'Pav. Seroja',
                tipe: 'Rawat Inap & Paviliun',
                kepala: 'AHMAD SAFI I, S. Kep.Ns',
                pj_aset: 'AHMAD SAFI I, S. Kep.Ns',
                nip: '197504231997031003',
                nik: '3511112304750004',
                email: 'madpii29@gmail.com',
                jabatan_pj: 'Kepala Pavilyun Seroja',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 35,
                kode: 'UNIT-035',
                nama: 'Pav. Krisan',
                tipe: 'Rawat Inap & Paviliun',
                kepala: 'MOHAMMAD HENRI WAHYONO, S.Kep.Ns., M.Kes',
                pj_aset: 'MOHAMMAD HENRI WAHYONO, S.Kep.Ns., M.Kes',
                nip: '198202032003121004',
                nik: '3509160302820001',
                email: 'abisyakila@gmail.com',
                jabatan_pj: 'Kepala Pavilyun Krisan',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 36,
                kode: 'UNIT-036',
                nama: 'PPI',
                tipe: 'Manajemen & Struktural',
                kepala: 'ENY YULIATI, S.Kep, Ns.,M.Mkes',
                pj_aset: 'ENY YULIATI, S.Kep, Ns.,M.Mkes',
                nip: '196907171991032014',
                nik: '3511115707690002',
                email: 'enymahija@gmail.com',
                jabatan_pj: 'Kepala Unit PPI',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 37,
                kode: 'UNIT-037',
                nama: 'Unit Endoskopi',
                tipe: 'Pelayanan Kritis & Tindakan Medis',
                kepala: 'HERI JUNIARTO, S.Kep.Ns',
                pj_aset: 'HERI JUNIARTO, S.Kep.Ns',
                nip: '197506122003121004',
                nik: '3511081206750005',
                email: 'herijuniarto2@gmail.com',
                jabatan_pj: 'Kaur Unit Endoscopy',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 38,
                kode: 'UNIT-038',
                nama: 'Unit CATH LAB',
                tipe: 'Pelayanan Kritis & Tindakan Medis',
                kepala: 'SYAFIIN, S.Kep.Ns',
                pj_aset: 'SYAFIIN, S.Kep.Ns',
                nip: '197202161995031002',
                nik: '3511081602720002',
                email: 'syafiin99@gmail.com',
                jabatan_pj: 'Kepala Ruang Unit Cathlab',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 39,
                kode: 'UNIT-039',
                nama: 'Inst. Kemoterapi',
                tipe: 'Pelayanan Kritis & Tindakan Medis',
                kepala: 'TIEN SUHADEWI, S.Kep.Ns',
                pj_aset: 'TIEN SUHADEWI, S.Kep.Ns',
                nip: '197707022007012004',
                nik: '3511114207770001',
                email: 'dewitien70@gmail.com',
                jabatan_pj: 'Kepala Pelayanan Instalasi Kemoterapi',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 40,
                kode: 'UNIT-040',
                nama: 'Inst. Anestesi',
                tipe: 'Pelayanan Kritis & Tindakan Medis',
                kepala: 'ASMONO, S.Kep.Ns',
                pj_aset: 'ASMONO, S.Kep.Ns',
                nip: '197003051996031005',
                nik: '3511110503700002',
                email: 'asmono69@gmail.com',
                jabatan_pj: 'Kepala Instalasi Anestesi',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 41,
                kode: 'UNIT-041',
                nama: 'Bidang Pelayanan Penunjang',
                tipe: 'Manajemen & Struktural',
                kepala: 'drg HESTY TULUS PANGGIH ARINI, MMRS',
                pj_aset: 'drg HESTY TULUS PANGGIH ARINI, MMRS',
                nip: '197607022009032003',
                nik: '3509214207760001',
                email: 'hestyhafi@gmail.com',
                jabatan_pj: 'Kepala Bidang Pelayanan Penunjang',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 42,
                kode: 'UNIT-042',
                nama: 'Inst. Rawat Jalan',
                tipe: 'Pelayanan Umum & Operasional',
                kepala: 'LINDA DWI ASTUTI, S.Kep.Ns',
                pj_aset: 'LINDA DWI ASTUTI, S.Kep.Ns',
                nip: '197705011997032003',
                nik: '3511114105770001',
                email: 'ny.linda77@gmail.com',
                jabatan_pj: 'Kepala Urusan Instalasi Rawat Jalan',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 43,
                kode: 'UNIT-043',
                nama: 'Unit Elektromedik IPSRS',
                tipe: 'Penunjang Medis & Fasilitas',
                kepala: 'SITI DENIK MU AWANAH, SST',
                pj_aset: 'SITI DENIK MU AWANAH, SST',
                nip: '197603172005012008',
                nik: '3511115703760002',
                email: 'sitidenik76@gmail.com',
                jabatan_pj: 'Kepala Pelayanan Elektromedik IPSRS',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 44,
                kode: 'UNIT-044',
                nama: 'Bagian Tata Usaha, Informasi dan Pemasaran',
                tipe: 'Manajemen & Struktural',
                kepala: 'DIDIK KURNIYANTO, S.Kep.Ns., M.M',
                pj_aset: 'DIDIK KURNIYANTO, S.Kep.Ns., M.M',
                nip: '198112062008011011',
                nik: '3511050612810002',
                email: 'bondowosohijrah@gmail.com',
                jabatan_pj: 'Kepala Sie Tata Usaha, Informasi dan Pemasaran',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 45,
                kode: 'UNIT-045',
                nama: 'Bagian Mobilisasi Dana',
                tipe: 'Manajemen & Struktural',
                kepala: 'INTUK WIJAYANTI, A.Md',
                pj_aset: 'INTUK WIJAYANTI, A.Md',
                nip: '197009141998032006',
                nik: '3511125409700002',
                email: 'intukwijayanti@gmail.com',
                jabatan_pj: 'Kepala Sie Mobilisasi Dana',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 46,
                kode: 'UNIT-046',
                nama: 'Bagian Perbendaharaan',
                tipe: 'Manajemen & Struktural',
                kepala: 'ERNA HAYUNIATI INDRIANINGTYAS, SE',
                pj_aset: 'ERNA HAYUNIATI INDRIANINGTYAS, SE',
                nip: '197709302009012001',
                nik: '3511057009770001',
                email: 'r_nasavalia@yahoo.co.id',
                jabatan_pj: 'Kepala Sie Perbendaharaan',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 47,
                kode: 'UNIT-047',
                nama: 'Bagian Penyusunan Program dan Anggaran',
                tipe: 'Manajemen & Struktural',
                kepala: 'ACHMAD ARFANDI, S.M',
                pj_aset: 'ACHMAD ARFANDI, S.M',
                nip: '197709102007011008',
                nik: '3511111009770004',
                email: 'ach.arfan@gmail.com',
                jabatan_pj: 'Kepala Sie Perencanaan dan Penyusunan Program',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 48,
                kode: 'UNIT-048',
                nama: 'Bagian Monitoring dan Evaluasi',
                tipe: 'Manajemen & Struktural',
                kepala: 'YENY DEVITANTI, SE, M.Si',
                pj_aset: 'YENY DEVITANTI, SE, M.Si',
                nip: '198001012009022007',
                nik: '3511084101800006',
                email: 'yenydevitanti@gmail.com',
                jabatan_pj: 'Analis Kebijakan Ahli Muda | Anggota Tim Pembangunan Zona Integritas | Sekretaris Tim Reformasi Birokrasi | Sub.Koord. Monitoring dan Evaluasi',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 49,
                kode: 'UNIT-049',
                nama: 'Lain - Lain',
                tipe: 'Pelayanan Umum & Operasional',
                kepala: 'WAHYUNI FAUZIAH, S. Kep., Ns., MHS.,Ph.D',
                pj_aset: 'WAHYUNI FAUZIAH, S. Kep., Ns., MHS.,Ph.D',
                nip: '198306222007012006',
                nik: '3509276206830004',
                email: 'wahyunifauziah83@gmail.com',
                jabatan_pj: 'Perawat',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 50,
                kode: 'UNIT-050',
                nama: 'Cleaning Service',
                tipe: 'Pelayanan Umum & Operasional',
                kepala: 'BUDIYANTO',
                pj_aset: 'BUDIYANTO',
                nip: '-',
                nik: '3511081806950003',
                email: 'budieyanto207@gmail.com',
                jabatan_pj: 'Kepala Cleaning Service',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 51,
                kode: 'UNIT-051',
                nama: 'Security',
                tipe: 'Pelayanan Umum & Operasional',
                kepala: 'YOYOK KURNIA MAHMUDI',
                pj_aset: 'YOYOK KURNIA MAHMUDI',
                nip: '198407212025211098',
                nik: '3511012107840001',
                email: 'shinta29bondowoso@gmail.com',
                jabatan_pj: 'Kepala Security',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 52,
                kode: 'UNIT-052',
                nama: 'Pengemudi',
                tipe: 'Pelayanan Umum & Operasional',
                kepala: 'TAYYIB',
                pj_aset: 'TAYYIB',
                nip: '197507102007011011',
                nik: '3511131007750001',
                email: 'tayyibtayyib58@gmail.com',
                jabatan_pj: 'Kepala Pengemudi',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 53,
                kode: 'UNIT-053',
                nama: 'Front Office',
                tipe: 'Pelayanan Umum & Operasional',
                kepala: 'ESTU PRATIKA SARI, S.ST',
                pj_aset: 'ESTU PRATIKA SARI, S.ST',
                nip: '199409242023212002',
                nik: '3509056409940001',
                email: 'estupratikasari@gmail.com',
                jabatan_pj: 'Kepala Front Office',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 54,
                kode: 'UNIT-054',
                nama: 'Pav. Camelia',
                tipe: 'Rawat Inap & Paviliun',
                kepala: 'M. AGUS HIDAYATULLAH, S.Kep.Ns',
                pj_aset: 'M. AGUS HIDAYATULLAH, S.Kep.Ns',
                nip: '197508162000121003',
                nik: '3511111608750009',
                email: 'attakhoir@gmail.com',
                jabatan_pj: 'Kepala Pav. Camelia',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            },
            {
                id: 55,
                kode: 'UNIT-055',
                nama: 'Orientasi',
                tipe: 'Pelayanan Umum & Operasional',
                kepala: 'AHMAD BAIHAQI',
                pj_aset: 'AHMAD BAIHAQI',
                nip: '-',
                nik: '3522121108960003',
                email: 'baiihaqiiahmad@gmail.com',
                jabatan_pj: 'Dokter Umum / PPDS',
                total_aset: 0,
                total_nilai: 'Rp 0',
                kapasitas: '-',
                assets: []
            }
        ],

        get filteredUnits() {
            const query = (this.searchQuery || '').toLowerCase();
            return this.units.filter(item => {
                return (item.nama || '').toLowerCase().includes(query) || 
                       (item.kode || '').toLowerCase().includes(query) ||
                       (item.kepala || '').toLowerCase().includes(query) ||
                       (item.tipe || '').toLowerCase().includes(query);
            });
        },

        // Filter aset di dalam modal detail ruangan
        get filteredDetailAssets() {
            if (!this.selectedUnit || !this.selectedUnit.assets) return [];
            const query = (this.detailSearchQuery || '').toLowerCase();
            return this.selectedUnit.assets.filter(ast => {
                const matchSearch = (ast.nama || '').toLowerCase().includes(query) ||
                                    (ast.kode || '').toLowerCase().includes(query) ||
                                    (ast.merk || '').toLowerCase().includes(query) ||
                                    (ast.no_seri || '').toLowerCase().includes(query);
                const matchKondisi = this.detailKondisiFilter === 'all' || ast.kondisi === this.detailKondisiFilter;
                const matchCategory = this.detailCategoryFilter === 'all' || ast.category === this.detailCategoryFilter;
                return matchSearch && matchKondisi && matchCategory;
            });
        },

        formatRupiah(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        },

        resetFilters() {
            this.searchQuery = '';
        },

        openDetail(item) {
            this.selectedUnit = item;
            this.detailSearchQuery = '';
            this.detailKondisiFilter = 'all';
            this.detailCategoryFilter = 'all';
            this.showDetailModal = true;
        },

        openPrintKIR(item) {
            this.selectedUnit = item ? { ...item } : (this.selectedUnit || this.units[0]);
            this.kirDoc.pj_nama = this.selectedUnit.pj_aset || this.selectedUnit.kepala;
            this.showPrintKIRModal = true;
        },

        printCurrentKIR() {
            window.print();
        },

        openEdit(item) {
            this.selectedUnit = { ...item };
            this.showEditModal = true;
        }
    }" x-cloak>

        <!-- Header Banner & Mini KPI Strip -->
        <div class="bg-gradient-to-r from-blue-600/15 via-slate-900 to-slate-900 border border-blue-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-6 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30 text-xs font-bold mb-3">
                        <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                        <span>LOKASI RUANGAN, PAVILIUN & INSTALASI RSUD</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Katalog Unit & Paviliun</h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                        Daftar lokasi penempatan aset tetap, kepala penanggung jawab ruangan, volume barang terpasang, serta total nilai aset tiap paviliun.
                    </p>
                </div>
                
                @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                <a href="{{ route('unit.create') }}"
                    class="px-4 py-2.5 rounded-xl bg-blue-500 hover:bg-blue-400 text-slate-950 font-bold text-xs shadow-lg shadow-blue-500/20 transition-all flex items-center space-x-2 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Unit Baru</span>
                </a>
                @endif
            </div>

            <!-- Mini Summary KPI Cards Strip -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-800/80">
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-blue-500/10 text-blue-400 text-lg">🏥</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Unit</span>
                        <span class="text-sm sm:text-base font-extrabold text-white" x-text="units.length + ' Lokasi'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-lg">💰</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Nilai Terdistribusi</span>
                        <span class="text-sm sm:text-base font-extrabold text-emerald-400 font-mono" x-text="'Rp ' + formatRupiah(units.reduce((acc, u) => acc + (parseInt(String(u.total_nilai).replace(/[^0-9]/g, '')) || 0), 0))"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400 text-lg">📦</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Aset Aktif</span>
                        <span class="text-sm sm:text-base font-extrabold text-cyan-300" x-text="units.reduce((acc, u) => acc + (u.assets ? u.assets.length : (u.total_aset || 0)), 0) + ' Item'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-purple-500/10 text-purple-400 text-lg">👨‍⚕️</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Penanggung Jawab</span>
                        <span class="text-sm sm:text-base font-extrabold text-purple-300" x-text="units.filter(u => u.kepala && u.kepala !== '-').length + ' Kepala Unit'"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter, Toggle Grid/Table & Search Bar Full-Width -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl mb-6">
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full">
                <div class="relative flex-1 w-full">
                    <input type="text" x-model="searchQuery" placeholder="Cari unit ruangan / kode lokasi / nama kepala ruangan..."
                        class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition-all">
                    <svg class="w-4 h-4 text-blue-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3.5 top-3 text-slate-500 hover:text-white text-xs font-bold">&times;</button>
                </div>

                <div class="flex items-center space-x-2 shrink-0">
                    <span class="px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-[11px] font-semibold text-slate-300">
                        Menampilkan <span class="text-blue-400 font-bold" x-text="filteredUnits.length"></span> dari <span class="text-white font-bold" x-text="units.length"></span> Unit
                    </span>
                    
                    <!-- View Toggle -->
                    <div class="flex items-center bg-slate-950 border border-slate-800 rounded-xl p-1">
                        <button type="button" @click="viewMode = 'grid'" 
                                :class="viewMode === 'grid' ? 'bg-blue-500 text-slate-950 font-bold' : 'text-slate-400 hover:text-white'"
                                class="px-2.5 py-1.5 rounded-lg text-xs transition-all flex items-center space-x-1">
                            <span>Grid</span>
                        </button>
                        <button type="button" @click="viewMode = 'table'" 
                                :class="viewMode === 'table' ? 'bg-blue-500 text-slate-950 font-bold' : 'text-slate-400 hover:text-white'"
                                class="px-2.5 py-1.5 rounded-lg text-xs transition-all flex items-center space-x-1">
                            <span>Tabel</span>
                        </button>
                    </div>

                    <button type="button" @click="resetFilters()"
                        class="px-3 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white text-xs font-semibold border border-slate-700 transition-all">
                        🔄 Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- VIEW MODE 1: GRID CARDS -->
        <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <template x-for="item in filteredUnits" :key="item.id">
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl hover:border-blue-500/50 transition-all flex flex-col justify-between group">
                    <div>
                        <div class="flex items-start justify-between mb-3">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30" x-text="item.kode"></span>
                            <span class="text-xs font-semibold text-slate-400" x-text="item.kapasitas"></span>
                        </div>
                        <h3 class="text-base font-extrabold text-white group-hover:text-blue-400 transition-colors" x-text="item.nama"></h3>
                        <p class="text-xs text-slate-400 mt-0.5 mb-4" x-text="item.tipe"></p>

                        <div class="space-y-2 py-3 border-y border-slate-800/80 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Kepala Unit:</span>
                                <span class="font-semibold text-white" x-text="item.kepala"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Total Aset:</span>
                                <span class="font-bold text-cyan-400" x-text="item.total_aset + ' Item'"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Nilai Aset:</span>
                                <span class="font-bold text-emerald-400 font-mono" x-text="item.total_nilai"></span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-end space-x-1">
                        <button type="button" @click="openPrintKIR(item)"
                            class="px-2.5 py-1.5 rounded-xl bg-blue-500/15 text-blue-300 hover:bg-blue-500/25 border border-blue-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>KIR</span>
                        </button>
                        <button type="button" @click="openDetail(item)"
                            class="px-2.5 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-300 hover:bg-emerald-500/20 border border-emerald-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>Detail</span>
                        </button>
                        @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                        <a :href="'/unit-paviliun/' + item.id + '/edit'"
                            class="px-2.5 py-1.5 rounded-xl bg-cyan-500/10 text-cyan-300 hover:bg-cyan-500/20 border border-cyan-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Ubah</span>
                        </a>
                        <button type="button"
                            class="px-2.5 py-1.5 rounded-xl bg-rose-500/10 text-rose-300 hover:bg-rose-500/20 border border-rose-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Hapus</span>
                        </button>
                        @endif
                    </div>
                </div>
            </template>
        </div>

        <!-- VIEW MODE 2: TABLE VIEW -->
        <div x-show="viewMode === 'table'" class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6 overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 text-center w-12">No</th>
                        <th class="px-4 py-3.5 text-center">Kode Unit</th>
                        <th class="px-4 py-3.5 text-center">Nama Unit / Paviliun</th>
                        <th class="px-4 py-3.5 text-center">Tipe Pelayanan</th>
                        <th class="px-4 py-3.5 text-center">Kepala Ruangan</th>
                        <th class="px-4 py-3.5 text-center">Total Aset</th>
                        <th class="px-4 py-3.5 text-center">Total Nilai</th>
                        <th class="px-4 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    <template x-for="(item, index) in filteredUnits" :key="item.id">
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-4 py-4 text-center font-bold text-slate-400" x-text="index + 1"></td>
                            <td class="px-4 py-4 text-center font-mono font-semibold text-blue-400" x-text="item.kode"></td>
                            <td class="px-4 py-4 font-bold text-white" x-text="item.nama"></td>
                            <td class="px-4 py-4 text-center text-slate-300" x-text="item.tipe"></td>
                            <td class="px-4 py-4 text-center font-semibold text-slate-200" x-text="item.kepala"></td>
                            <td class="px-4 py-4 text-center font-bold text-cyan-400" x-text="item.total_aset + ' Item'"></td>
                            <td class="px-4 py-4 text-center font-bold text-emerald-400 font-mono" x-text="item.total_nilai"></td>
                            <td class="px-4 py-4 text-center space-x-1 whitespace-nowrap">
                                <button type="button" @click="openPrintKIR(item)"
                                    class="px-2.5 py-1.5 rounded-xl bg-blue-500/15 text-blue-300 hover:bg-blue-500/25 border border-blue-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    <span>KIR</span>
                                </button>
                                <button type="button" @click="openDetail(item)"
                                    class="px-2.5 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-300 hover:bg-emerald-500/20 border border-emerald-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Detail</span>
                                </button>
                                @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                                <a :href="'/unit-paviliun/' + item.id + '/edit'"
                                    class="px-2.5 py-1.5 rounded-xl bg-cyan-500/10 text-cyan-300 hover:bg-cyan-500/20 border border-cyan-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>Ubah</span>
                                </a>
                                <button type="button"
                                    class="px-2.5 py-1.5 rounded-xl bg-rose-500/10 text-rose-300 hover:bg-rose-500/20 border border-rose-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span>Hapus</span>
                                </button>
                                @endif
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL DETAIL UNIT & INVENTARIS ASET RUANGAN (LENGKAP DENGAN DAFTAR ASET)   -->
        <!-- ========================================================================= -->
        <div x-show="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-3 sm:p-5 overflow-y-auto" x-cloak>
            <div @click.away="showDetailModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-5xl w-full p-6 sm:p-7 shadow-2xl space-y-5 my-auto max-h-[90vh] flex flex-col">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 shrink-0">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 rounded-2xl bg-blue-500/20 text-blue-300 text-xl">🏥</div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30" x-text="selectedUnit ? selectedUnit.kode : ''"></span>
                                <h3 class="text-lg sm:text-xl font-extrabold text-white" x-text="selectedUnit ? selectedUnit.nama : ''"></h3>
                            </div>
                            <p class="text-xs text-slate-400 mt-0.5" x-text="selectedUnit ? (selectedUnit.tipe + ' • Kapasitas: ' + selectedUnit.kapasitas) : ''"></p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <button type="button" @click="printKIR()"
                            class="hidden sm:inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 border border-blue-500/40 text-xs font-bold transition-all active:scale-95">
                            <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Cetak KIR</span>
                        </button>
                        <button type="button" @click="showDetailModal = false" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white text-base font-bold transition-all">
                            &times;
                        </button>
                    </div>
                </div>

                <!-- Informasi Ringkas Profil Ruangan -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 shrink-0" x-if="selectedUnit">
                    <div class="p-3 bg-slate-950/70 rounded-2xl border border-slate-800/80">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Kepala Ruangan / PJ</span>
                        <p class="font-bold text-white text-xs mt-0.5 truncate" x-text="selectedUnit.kepala"></p>
                        <p class="text-[10px] text-slate-400 font-mono truncate mt-0.5" x-text="'NIP: ' + selectedUnit.nip"></p>
                    </div>
                    <div class="p-3 bg-slate-950/70 rounded-2xl border border-slate-800/80">
                        <span class="text-[10px] uppercase font-bold text-blue-400 block tracking-wider">Kontak & Jabatan</span>
                        <p class="font-bold text-blue-300 text-xs mt-0.5 truncate" x-text="selectedUnit.email"></p>
                        <p class="text-[10px] text-slate-400 truncate mt-0.5" x-text="selectedUnit.jabatan_pj"></p>
                    </div>
                    <div class="p-3 bg-slate-950/70 rounded-2xl border border-slate-800/80">
                        <span class="text-[10px] uppercase font-bold text-cyan-400 block tracking-wider">Total Aset Terpasang</span>
                        <p class="font-black text-cyan-300 text-xs mt-0.5" x-text="(selectedUnit.assets ? selectedUnit.assets.length : selectedUnit.total_aset) + ' Item Inventaris'"></p>
                        <span class="text-[10px] text-slate-500 block">KIR Ruangan</span>
                    </div>
                    <div class="p-3 bg-slate-950/70 rounded-2xl border border-slate-800/80">
                        <span class="text-[10px] uppercase font-bold text-emerald-400 block tracking-wider">Total Nilai Realisasi</span>
                        <p class="font-black text-emerald-400 font-mono text-xs mt-0.5" x-text="selectedUnit.total_nilai"></p>
                        <span class="text-[10px] text-slate-500 block">Akumulasi Aset</span>
                    </div>
                </div>

                <!-- Toolbar Pencarian & Filter Aset Ruangan -->
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex flex-col sm:flex-row items-center justify-between gap-3 shrink-0">
                    <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto flex-1">
                        <div class="relative flex-1 sm:w-64">
                            <input type="text" x-model="detailSearchQuery" placeholder="Cari nama aset, merk, kode 108, nomor seri..."
                                class="w-full bg-slate-900 border border-slate-700/80 rounded-xl px-3 py-2 pl-9 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500">
                            <svg class="w-3.5 h-3.5 text-blue-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <button type="button" x-show="detailSearchQuery" @click="detailSearchQuery = ''" class="absolute right-2.5 top-2 text-slate-500 hover:text-white text-xs">&times;</button>
                        </div>

                        <select x-model="detailKondisiFilter" class="bg-slate-900 border border-slate-700/80 rounded-xl px-3 py-2 text-xs text-slate-300 focus:outline-none focus:border-blue-500">
                            <option value="all">Semua Kondisi</option>
                            <option value="Baik">Kondisi Baik</option>
                            <option value="Rusak Ringan">Rusak Ringan</option>
                            <option value="Rusak Berat">Rusak Berat</option>
                        </select>
                    </div>

                    <div class="text-[11px] text-slate-400 shrink-0 font-medium">
                        Ditemukan <span class="text-blue-400 font-bold" x-text="filteredDetailAssets.length"></span> dari <span class="text-white font-bold" x-text="(selectedUnit && selectedUnit.assets) ? selectedUnit.assets.length : 0"></span> Aset Terdata
                    </div>
                </div>

                <!-- Tabel Daftar Rincian Aset Terpasang di Ruangan -->
                <div class="flex-1 overflow-y-auto bg-slate-950/80 border border-slate-800 rounded-2xl overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-300 min-w-[750px]">
                        <thead class="bg-slate-950 text-slate-400 font-bold uppercase tracking-wider text-[10px] sticky top-0 z-10 border-b border-slate-800">
                            <tr>
                                <th class="px-3 py-3 text-center w-10">No</th>
                                <th class="px-3 py-3 text-center">Kode 108 / Register</th>
                                <th class="px-4 py-3">Nama Barang & Spesifikasi</th>
                                <th class="px-3 py-3 text-center">Kategori</th>
                                <th class="px-3 py-3 text-center">Tahun</th>
                                <th class="px-3 py-3 text-center">Kondisi</th>
                                <th class="px-3 py-3 text-right">Nilai Aset</th>
                                <th class="px-3 py-3 text-center">Status Operasional</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80">
                            <template x-for="(ast, idx) in filteredDetailAssets" :key="idx">
                                <tr class="hover:bg-slate-800/40 transition-colors">
                                    <td class="px-3 py-3 text-center text-slate-400 font-mono font-bold" x-text="idx + 1"></td>
                                    <td class="px-3 py-3 text-center">
                                        <span class="font-mono text-cyan-400 font-bold text-[11px] block" x-text="ast.kode"></span>
                                        <span class="font-mono text-[9px] text-slate-400" x-text="ast.no_seri"></span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-white text-xs" x-text="ast.nama"></div>
                                        <div class="text-[10px] text-blue-300 font-medium" x-text="ast.merk"></div>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-800 text-slate-300 border border-slate-700" x-text="ast.category"></span>
                                    </td>
                                    <td class="px-3 py-3 text-center font-mono font-semibold text-slate-300" x-text="ast.tahun"></td>
                                    <td class="px-3 py-3 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold"
                                            :class="{
                                                'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30': ast.kondisi === 'Baik',
                                                'bg-amber-500/20 text-amber-300 border border-amber-500/30': ast.kondisi === 'Rusak Ringan',
                                                'bg-rose-500/20 text-rose-300 border border-rose-500/30': ast.kondisi === 'Rusak Berat'
                                            }"
                                            x-text="ast.kondisi"></span>
                                    </td>
                                    <td class="px-3 py-3 text-right font-mono font-bold text-emerald-400" x-text="'Rp ' + formatRupiah(ast.nilai)"></td>
                                    <td class="px-3 py-3 text-center">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold"
                                            :class="{
                                                'bg-cyan-500/15 text-cyan-300 border border-cyan-500/30': ast.status === 'Aktif Digunakan',
                                                'bg-purple-500/15 text-purple-300 border border-purple-500/30': ast.status === 'Standby Cadangan',
                                                'bg-amber-500/15 text-amber-300 border border-amber-500/30': ast.status.includes('Servis') || ast.status.includes('Kalibrasi')
                                            }"
                                            x-text="ast.status"></span>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="filteredDetailAssets.length === 0">
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                                        <div class="text-2xl mb-1">🔍</div>
                                        <p class="font-semibold text-white">Tidak ada aset yang cocok dengan filter</p>
                                        <p class="text-[11px] text-slate-500 mt-0.5">Coba ubah kata kunci pencarian atau reset filter kondisi.</p>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Modal Action Footer -->
                <div class="pt-3 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3 shrink-0">
                    <div class="flex items-center space-x-2 text-xs text-slate-400">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span>Inventaris ruangan terintegrasi dengan Kartu Inventaris Ruangan (KIR) & BAST Distribusi.</span>
                    </div>

                    <div class="flex items-center space-x-2 w-full sm:w-auto justify-end">
                        @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                        <a href="{{ route('bast.index') }}"
                            class="px-3.5 py-2 rounded-xl bg-purple-500/15 hover:bg-purple-500/25 text-purple-300 border border-purple-500/30 font-bold text-xs transition-all flex items-center space-x-1.5 active:scale-95">
                            <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>📑 BAST Ruangan</span>
                        </a>
                        @endif

                        <button type="button" @click="openPrintKIR(selectedUnit)"
                            class="px-3.5 py-2 rounded-xl bg-blue-500 hover:bg-blue-400 text-slate-950 font-bold text-xs shadow-lg shadow-blue-500/20 transition-all flex items-center space-x-1.5 active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>🖨️ Cetak KIR</span>
                        </button>

                        <button type="button" @click="showDetailModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all active:scale-95">
                            Tutup
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL CETAK LEMBAR KARTU INVENTARIS RUANGAN (KIR) RESMI KEDINASAN BMD     -->
        <!-- ========================================================================= -->
        <div x-show="showPrintKIRModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-2 sm:p-4 overflow-y-auto" x-cloak>
            <div @click.away="showPrintKIRModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-5xl w-full p-4 sm:p-6 shadow-2xl space-y-4 my-auto relative">
                
                <!-- Action Bar Modal -->
                <div class="no-print flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
                    <div class="flex items-center space-x-2">
                        <span class="p-2 rounded-xl bg-blue-500/20 text-blue-300 text-sm">🖨️</span>
                        <div>
                            <h3 class="text-base font-extrabold text-white">Lembar Cetak Kartu Inventaris Ruangan (KIR)</h3>
                            <p class="text-[11px] text-slate-400" x-text="selectedUnit ? (selectedUnit.nama + ' • Kode: ' + selectedUnit.kode) : ''"></p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 w-full sm:w-auto justify-end">
                        <button type="button" @click="showEditKIRForm = !showEditKIRForm"
                            class="px-3.5 py-2 rounded-xl bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 border border-blue-500/40 font-bold text-xs transition-all flex items-center space-x-1.5 active:scale-95">
                            <span x-text="showEditKIRForm ? '✕ Tutup Form Edit' : '✏️ Edit Data Pejabat / Tanggal KIR'"></span>
                        </button>
                        <button type="button" @click="printCurrentKIR()"
                            class="px-4 py-2 rounded-xl bg-blue-500 hover:bg-blue-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-blue-500/20 transition-all flex items-center space-x-1.5 active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Cetak (Print / PDF)</span>
                        </button>
                        <button type="button" @click="showPrintKIRModal = false" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all active:scale-95">
                            Tutup
                        </button>
                    </div>
                </div>

                <!-- Formulir Cepat Edit Pejabat KIR (Hidden when Printed) -->
                <div x-show="showEditKIRForm" class="no-print bg-slate-950 p-4 rounded-2xl border border-blue-500/40 text-xs space-y-3 shadow-inner">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                        <span class="font-bold text-blue-300 text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                            <span>✏️ Sesuaikan Data Nomor KIR, Tanggal & Identitas Pejabat Pengesah:</span>
                        </span>
                        <span class="text-[10px] text-emerald-400 font-mono">Teks di lembar KIR otomatis berganti live</span>
                    </div>

                    <!-- Nomor & Tanggal -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Nomor Registrasi KIR</label>
                            <input type="text" x-model="kirDoc.nomor_surat" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-blue-300 font-mono font-bold text-xs">
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Tanggal Pengesahan (Kota, Tanggal Bulan Tahun)</label>
                            <input type="text" x-model="kirDoc.tanggal_pengesahan" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white text-xs">
                        </div>
                    </div>

                    <!-- 3 Pejabat: Penanggung Jawab Ruangan, Pengurus Barang, Direktur -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-2 border-t border-slate-800/80">
                        <!-- PJ Ruangan -->
                        <div class="p-3 rounded-xl bg-slate-900/80 border border-blue-500/30 space-y-2">
                            <span class="text-[10px] font-bold text-blue-400 block uppercase">1. Penanggung Jawab Ruangan:</span>
                            <div>
                                <label class="block text-slate-400 text-[9px]">Nama PJ Ruangan / Gelar</label>
                                <input type="text" x-model="kirDoc.pj_nama" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-blue-300 font-semibold text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[9px]">NIP PJ Ruangan</label>
                                <input type="text" x-model="kirDoc.pj_nip" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white font-mono text-xs">
                            </div>
                        </div>

                        <!-- Pengurus Barang -->
                        <div class="p-3 rounded-xl bg-slate-900/80 border border-emerald-500/30 space-y-2">
                            <span class="text-[10px] font-bold text-emerald-400 block uppercase">2. Pengurus Barang RSUD:</span>
                            <div>
                                <label class="block text-slate-400 text-[9px]">Nama Pengurus Barang</label>
                                <input type="text" x-model="kirDoc.pengurus_nama" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-emerald-400 font-bold text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[9px]">NIP Pengurus Barang</label>
                                <input type="text" x-model="kirDoc.pengurus_nip" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white font-mono text-xs">
                            </div>
                        </div>

                        <!-- Direktur RSUD -->
                        <div class="p-3 rounded-xl bg-slate-900/80 border border-purple-500/30 space-y-2">
                            <span class="text-[10px] font-bold text-purple-400 block uppercase">3. Mengetahui (Direktur RSUD):</span>
                            <div>
                                <label class="block text-slate-400 text-[9px]">Nama Direktur & Gelar</label>
                                <input type="text" x-model="kirDoc.direktur_nama" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white font-bold text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[9px]">NIP Direktur</label>
                                <input type="text" x-model="kirDoc.direktur_nip" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white font-mono text-xs">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- LEMBAR CETAK ASLI DOKUMEN KARTU INVENTARIS RUANGAN (KIR) KERTAS PUTIH -->
                <template x-if="selectedUnit">
                    <div id="print-area-kir" class="bg-white text-black p-6 sm:p-8 rounded-2xl shadow-xl max-h-[70vh] overflow-y-auto font-serif text-[11px] leading-relaxed select-text print:max-h-none print:overflow-visible print:p-0 print:m-0 print:shadow-none print:rounded-none">
                        
                        <!-- KOP SURAT RESMI -->
                        <div class="border-b-[3px] border-black pb-1 mb-0.5">
                            <div class="flex items-center justify-between gap-4">
                                <div class="w-20 shrink-0 flex justify-center">
                                    <img src="{{ asset('img/Logo-rsud/logo-rsud.png') }}" alt="Logo RSUD" class="w-16 h-16 object-contain">
                                </div>

                                <div class="flex-1 text-center font-sans text-black">
                                    <h4 class="font-bold text-xs sm:text-sm uppercase tracking-wide leading-tight">PEMERINTAH KABUPATEN BONDOWOSO</h4>
                                    <h3 class="font-black text-sm sm:text-base uppercase tracking-tight leading-tight">RUMAH SAKIT UMUM DAERAH dr.H.KOESNADI</h3>
                                    <p class="text-[10px] leading-tight mt-0.5">Jl. Kapten Piere Tendean No. 3 Telp. (0332) 421974 Fax.0332 422311</p>
                                    <p class="text-[10px] leading-tight">e-mail : rsu.koesnadi@gmail.com, Website : rsudrkoesnadi.go.id</p>
                                    <h4 class="font-bold text-xs tracking-[0.3em] uppercase mt-0.5">B O N D O W O S O</h4>
                                </div>

                                <div class="w-16 shrink-0"></div>
                            </div>
                        </div>
                        <div class="border-b border-black mb-4"></div>

                        <!-- JUDUL LEMBAR KIR -->
                        <div class="text-center font-sans mb-3">
                            <h3 class="font-black text-xs sm:text-sm uppercase underline tracking-wider">KARTU INVENTARIS RUANGAN (KIR)</h3>
                            <p class="text-[10px] font-semibold">Nomor Registrasi : <span x-text="kirDoc.nomor_surat"></span></p>
                        </div>

                        <!-- ATRIBUT DATA RUANGAN -->
                        <div class="grid grid-cols-2 gap-x-6 gap-y-1 font-sans text-[10px] mb-3 border p-2.5 bg-gray-50 border-gray-300 rounded">
                            <div class="flex">
                                <span class="w-32 font-bold">SKPD / Unit Kerja</span>
                                <span class="w-3">:</span>
                                <span class="font-semibold text-black">RSUD dr. H. KOESNANDI</span>
                            </div>
                            <div class="flex">
                                <span class="w-32 font-bold">KABUPATEN / PROV</span>
                                <span class="w-3">:</span>
                                <span class="font-semibold text-black">BONDOWOSO / JAWA TIMUR</span>
                            </div>
                            <div class="flex">
                                <span class="w-32 font-bold">NAMA RUANGAN / UNIT</span>
                                <span class="w-3">:</span>
                                <span class="font-bold text-black uppercase" x-text="selectedUnit.nama"></span>
                            </div>
                            <div class="flex">
                                <span class="w-32 font-bold">KODE RUANGAN / LOKASI</span>
                                <span class="w-3">:</span>
                                <span class="font-mono font-bold text-black" x-text="selectedUnit.kode"></span>
                            </div>
                            <div class="flex">
                                <span class="w-32 font-bold">PENANGGUNG JAWAB</span>
                                <span class="w-3">:</span>
                                <span class="font-semibold text-black" x-text="kirDoc.pj_nama"></span>
                            </div>
                            <div class="flex">
                                <span class="w-32 font-bold">TAHUN ANGGARAN</span>
                                <span class="w-3">:</span>
                                <span class="font-bold text-black">2026</span>
                            </div>
                        </div>

                        <!-- TABEL STANDAR PERMENDAGRI BMD UNTUK KARTU INVENTARIS RUANGAN -->
                        <div class="mb-4">
                            <table class="w-full border-collapse border border-black text-[9.5px]">
                                <thead class="bg-gray-100 font-sans text-center font-bold">
                                    <tr>
                                        <th rowspan="2" class="border border-black px-1.5 py-2 w-7">No</th>
                                        <th rowspan="2" class="border border-black px-2 py-2">Jenis Barang / Nama Barang</th>
                                        <th rowspan="2" class="border border-black px-2 py-2">Merk / Type / Spesifikasi</th>
                                        <th rowspan="2" class="border border-black px-2 py-2">No. Pabrik / No. Seri</th>
                                        <th rowspan="2" class="border border-black px-2 py-2 w-12">Tahun</th>
                                        <th rowspan="2" class="border border-black px-2 py-2">Kode 108 / Register</th>
                                        <th rowspan="2" class="border border-black px-1.5 py-2 w-10">Jml</th>
                                        <th rowspan="2" class="border border-black px-2 py-2 text-right">Harga Beli / Nilai (Rp)</th>
                                        <th colspan="3" class="border border-black px-1 py-1">Keadaan Barang</th>
                                        <th rowspan="2" class="border border-black px-2 py-2">Keterangan</th>
                                    </tr>
                                    <tr>
                                        <th class="border border-black px-1 py-1 w-8">B</th>
                                        <th class="border border-black px-1 py-1 w-8">RR</th>
                                        <th class="border border-black px-1 py-1 w-8">RB</th>
                                    </tr>
                                    <tr class="bg-gray-50 text-[8px] font-mono">
                                        <th class="border border-black">1</th>
                                        <th class="border border-black">2</th>
                                        <th class="border border-black">3</th>
                                        <th class="border border-black">4</th>
                                        <th class="border border-black">5</th>
                                        <th class="border border-black">6</th>
                                        <th class="border border-black">7</th>
                                        <th class="border border-black">8</th>
                                        <th class="border border-black">9</th>
                                        <th class="border border-black">10</th>
                                        <th class="border border-black">11</th>
                                        <th class="border border-black">12</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(ast, idx) in (selectedUnit.assets || [])" :key="idx">
                                        <tr>
                                            <td class="border border-black px-1 py-1 text-center font-mono" x-text="idx + 1"></td>
                                            <td class="border border-black px-2 py-1 font-sans font-bold" x-text="ast.nama"></td>
                                            <td class="border border-black px-2 py-1 font-sans" x-text="ast.merk"></td>
                                            <td class="border border-black px-2 py-1 font-mono text-[9px]" x-text="ast.no_seri"></td>
                                            <td class="border border-black px-1 py-1 text-center font-mono" x-text="ast.tahun"></td>
                                            <td class="border border-black px-2 py-1 font-mono text-center text-[9px]" x-text="ast.kode"></td>
                                            <td class="border border-black px-1 py-1 text-center font-mono font-bold">1</td>
                                            <td class="border border-black px-2 py-1 text-right font-mono font-semibold" x-text="formatRupiah(ast.nilai)"></td>
                                            
                                            <!-- Keadaan Barang: Baik (B), Rusak Ringan (RR), Rusak Berat (RB) -->
                                            <td class="border border-black px-1 py-1 text-center font-bold font-sans">
                                                <span x-text="ast.kondisi === 'Baik' ? '✓' : ''"></span>
                                            </td>
                                            <td class="border border-black px-1 py-1 text-center font-bold font-sans">
                                                <span x-text="ast.kondisi === 'Rusak Ringan' ? '✓' : ''"></span>
                                            </td>
                                            <td class="border border-black px-1 py-1 text-center font-bold font-sans">
                                                <span x-text="ast.kondisi === 'Rusak Berat' ? '✓' : ''"></span>
                                            </td>

                                            <td class="border border-black px-2 py-1 font-sans text-[8.5px]" x-text="ast.status"></td>
                                        </tr>
                                    </template>
                                    
                                    <template x-if="!selectedUnit.assets || selectedUnit.assets.length === 0">
                                        <tr>
                                            <td colspan="12" class="border border-black px-2 py-3 text-center italic text-gray-500">
                                                (Belum ada data barang/aset yang terdata di ruangan ini)
                                            </td>
                                        </tr>
                                    </template>

                                    <!-- Baris Total -->
                                    <tr class="bg-gray-100 font-bold font-sans">
                                        <td colspan="6" class="border border-black px-2 py-1.5 text-right uppercase">JUMLAH TOTAL DI RUANGAN:</td>
                                        <td class="border border-black px-1 py-1.5 text-center font-mono" x-text="(selectedUnit.assets ? selectedUnit.assets.length : 0) + ' Unit'"></td>
                                        <td class="border border-black px-2 py-1.5 text-right font-mono font-bold" x-text="selectedUnit.total_nilai"></td>
                                        <td colspan="4" class="border border-black px-2 py-1.5 text-center text-[8.5px] italic text-gray-700">Terinventarisasi Lengkap</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- CATATAN KETENTUAN KIR -->
                        <div class="text-[8.5px] text-gray-700 mb-4 leading-tight font-sans">
                            <p class="font-bold">Keterangan & Ketentuan Pengelolaan Ruangan:</p>
                            <p>1. Barang inventaris yang tercatat dalam daftar ini berada di bawah pengawasan & tanggung jawab Kepala Ruangan.</p>
                            <p>2. Dilarang memindahkan barang inventaris dari ruangan ini ke ruangan lain tanpa izin resmi & Berita Acara Mutasi.</p>
                            <p>3. Apabila terjadi kerusakan/kehilangan segera melapor kepada Pengurus Barang / IPSRS RSUD dr. H. Koesnandi Bondowoso.</p>
                        </div>

                        <!-- 3 KOLOM TANDA TANGAN PENGESAHAN -->
                        <div class="grid grid-cols-3 gap-3 text-center font-sans text-[10px] pt-1">
                            
                            <!-- Kolom 1: Direktur RSUD -->
                            <div>
                                <p class="font-bold">Mengetahui / Menyetujui,</p>
                                <p class="font-black uppercase text-[9.5px]">DIREKTUR RSUD dr.H.KOESNANDI</p>
                                
                                <div class="h-14 flex items-center justify-center my-1">
                                    <div class="flex items-center space-x-1.5 p-1 border border-black bg-gray-50 rounded">
                                        <div class="w-8 h-8 bg-white border border-black p-0.5">
                                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=BSRE-KIR-DIREKTUR-KOESNANDI" class="w-full h-full object-contain">
                                        </div>
                                        <div class="text-left text-[6.5px] leading-tight text-black">
                                            <div class="font-bold">PENGGUNA BARANG</div>
                                            <div>Tervalidasi BSrE</div>
                                        </div>
                                    </div>
                                </div>

                                <p class="font-bold underline text-[10.5px]" x-text="kirDoc.direktur_nama"></p>
                                <p class="font-mono text-[9px]" x-text="'NIP. ' + kirDoc.direktur_nip"></p>
                            </div>

                            <!-- Kolom 2: Pengurus Barang Aset -->
                            <div>
                                <p class="font-bold">Pengurus Barang Pengelola Aset,</p>
                                <p class="font-black uppercase text-[9.5px]">RSUD dr.H.KOESNANDI</p>
                                
                                <div class="h-14 flex items-center justify-center my-1">
                                    <div class="flex items-center space-x-1.5 p-1 border border-black bg-gray-50 rounded">
                                        <div class="w-8 h-8 bg-white border border-black p-0.5">
                                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=BSRE-KIR-PENGURUS-BARANG" class="w-full h-full object-contain">
                                        </div>
                                        <div class="text-left text-[6.5px] leading-tight text-black">
                                            <div class="font-bold">PENGURUS BARANG</div>
                                            <div>Tervalidasi BSrE</div>
                                        </div>
                                    </div>
                                </div>

                                <p class="font-bold underline text-[10.5px]" x-text="kirDoc.pengurus_nama"></p>
                                <p class="font-mono text-[9px]" x-text="'NIP. ' + kirDoc.pengurus_nip"></p>
                            </div>

                            <!-- Kolom 3: Penanggung Jawab Ruangan -->
                            <div>
                                <p class="font-semibold text-[9.5px]" x-text="kirDoc.tanggal_pengesahan"></p>
                                <p class="font-bold uppercase text-[9.5px]">PENANGGUNG JAWAB RUANGAN,</p>
                                
                                <div class="h-14 flex items-center justify-center my-1">
                                    <div class="flex items-center space-x-1.5 p-1 border border-black bg-gray-50 rounded">
                                        <div class="w-8 h-8 bg-white border border-black p-0.5">
                                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=BSRE-KIR-PJ-RUANGAN" class="w-full h-full object-contain">
                                        </div>
                                        <div class="text-left text-[6.5px] leading-tight text-black">
                                            <div class="font-bold">PJ RUANGAN</div>
                                            <div>Tervalidasi BSrE</div>
                                        </div>
                                    </div>
                                </div>

                                <p class="font-bold underline text-[10.5px]" x-text="kirDoc.pj_nama"></p>
                                <p class="font-mono text-[9px]" x-text="'NIP. ' + kirDoc.pj_nip"></p>
                            </div>

                        </div>

                    </div>
                </template>

            </div>
        </div>

        <!-- MODAL TAMBAH UNIT -->
        <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4" x-cloak>
            <div @click.away="showAddModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                    <h3 class="text-base font-bold text-white">+ Tambah Unit / Paviliun Baru</h3>
                    <button type="button" @click="showAddModal = false" class="text-slate-500 hover:text-white">&times;</button>
                </div>
                <form @submit.prevent="showAddModal = false" class="space-y-4 text-xs">
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Nama Unit / Paviliun</label>
                        <input type="text" placeholder="Paviliun..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Tipe Pelayanan Ruangan</label>
                        <input type="text" placeholder="Rawat Inap / Penunjang / Administrasi" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Nama Kepala Ruangan</label>
                        <input type="text" placeholder="dr..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                    </div>
                    <div class="pt-4 flex items-center justify-end space-x-2">
                        <button type="button" @click="showAddModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-blue-500 text-slate-950 font-bold">Simpan Unit</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL UBAH UNIT -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4" x-cloak>
            <div @click.away="showEditModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                    <h3 class="text-base font-bold text-white">✏️ Ubah Data Unit</h3>
                    <button type="button" @click="showEditModal = false" class="text-slate-500 hover:text-white">&times;</button>
                </div>
                <form @submit.prevent="showEditModal = false" class="space-y-4 text-xs">
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Nama Unit</label>
                        <input type="text" x-model="selectedUnit ? selectedUnit.nama : ''" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Kepala Ruangan</label>
                        <input type="text" x-model="selectedUnit ? selectedUnit.kepala : ''" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                    </div>
                    <div class="pt-4 flex items-center justify-end space-x-2">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-blue-500 text-slate-950 font-bold">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layout>
