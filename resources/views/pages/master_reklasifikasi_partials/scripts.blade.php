<!-- ALPINE.JS SCRIPTS MASTER REKLASIFIKASI -->
<script>
    function masterReklasifikasi() {
        return {
            activeTab: 'matriks',
            showModalTambah: false,
            showConfirmDelete: false,
            showPanduanModal: false,
            panduanSearch: '',
            panduanFilterKib: 'all',
            deleteTargetId: null,
            deleteTargetName: '',
            isSubmitting: false,

            openPanduan(prefix = '') {
                this.panduanSearch = prefix || '';
                this.panduanFilterKib = 'all';
                this.showPanduanModal = true;
            },

            get filteredPanduanList() {
                const q = (this.panduanSearch || '').toLowerCase().trim();
                const kib = this.panduanFilterKib;
                return this.panduanList.filter(item => {
                    const matchKib = (kib === 'all' || item.kib === kib);
                    if (!matchKib) return false;
                    if (!q) return true;
                    return item.prefix.toLowerCase().includes(q) ||
                           item.nama.toLowerCase().includes(q) ||
                           item.contohRs.toLowerCase().includes(q) ||
                           item.subRincian.toLowerCase().includes(q);
                });
            },

            panduanList: [
                {
                    prefix: '1.3.1.01',
                    kib: 'KIB A',
                    nama: 'TANAH',
                    contohRs: 'Tanah Bangunan Utama RSUD, Rumah Dinas Direktur/Dokter, Mess Pegawai RS, Pos Satpam, Area Parkir Pengunjung & Ambulans.',
                    subRincian: 'Tanah Bangunan Gedung Tempat Kerja, Tanah Rumah Dinas, Lapangan.'
                },
                {
                    prefix: '1.3.2.01',
                    kib: 'KIB B',
                    nama: 'ALAT BESAR',
                    contohRs: 'Genset Utama / Pembangkit Listrik Sentral Darurat RS, Forklift Gudang Farmasi/Logistik, Kompresor Sentral Gas Medis, Pompa Sedot Sentral IPAL / Pengolahan Limbah Cair RS.',
                    subRincian: 'Tractor, Excavator, Grader, Loader, Alat Pengangkat (Forklift, Crane), Mesin Proses, Compressor, Electric Generating Set (Genset), Pompa.'
                },
                {
                    prefix: '1.3.2.02',
                    kib: 'KIB B',
                    nama: 'ALAT ANGKUTAN',
                    contohRs: 'Mobil Ambulans Emergency 118, Mobil Jenazah, Mobil Operasional Dinas Direksi, Sepeda Motor Kurir Obat/Dokumen, Mobil Layanan Farmasi Keliling, Truk Angkut Sampah RS.',
                    subRincian: 'Kendaraan Dinas Bermotor, Ambulans, Truk, Sepeda Motor, Kendaraan Operasional.'
                },
                {
                    prefix: '1.3.2.03',
                    kib: 'KIB B',
                    nama: 'ALAT BENGKEL DAN ALAT UKUR',
                    contohRs: 'Mesin Las Listrik IPSRS (Pemeliharaan Sarana RS), Mesin Bubut/Gerinda Bengkel RS, Multitester Kalibrasi Alat Medis, Timbangan Kalibrasi Presisi.',
                    subRincian: 'Alat Bengkel Bermesin/Tak Bermesin, Alat Ukur Universal, Alat Ukur Elektronik.'
                },
                {
                    prefix: '1.3.2.04',
                    kib: 'KIB B',
                    nama: 'ALAT PERTANIAN',
                    contohRs: 'Mesin Potong Rumput Halaman RS, Mesin Babat Rumput Gendong, Sprinkler Penyiram Rumput Taman Rumah Sakit.',
                    subRincian: 'Alat Pengolahan Tanah, Alat Pemeliharaan Tanaman.'
                },
                {
                    prefix: '1.3.2.05',
                    kib: 'KIB B',
                    nama: 'ALAT KANTOR DAN RUMAH TANGGA',
                    contohRs: 'Meja & Kursi Kerja Dokter/Perawat, Lemari Besi Rekam Medis (RM), AC Split Bangsal, Kulkas Penyimpan Vaksin/Obat, Water Dispenser Pasien, Brankas Keuangan RS, Lemari Narkotika.',
                    subRincian: 'Alat Kantor (Meja, Kursi, Lemari, Filing Cabinet), Alat Rumah Tangga (AC, Kulkas, Kipas Angin, Dispenser).'
                },
                {
                    prefix: '1.3.2.06',
                    kib: 'KIB B',
                    nama: 'ALAT STUDIO, KOMUNIKASI DAN PEMANCAR',
                    contohRs: 'Handy Talkie (HT) Satpam/IPSRS, PABX Sentral Telepon RS, Sound System Informasi Ruang Tunggu, Kamera CCTV Keamanan RS, TV LED Antrian Pasien.',
                    subRincian: 'Alat Komunikasi Telepon/HT, Alat Studio Video/Audio, Alat Pemancar Informasi.'
                },
                {
                    prefix: '1.3.2.07',
                    kib: 'KIB B',
                    nama: 'ALAT KEDOKTERAN DAN KESEHATAN',
                    contohRs: 'USG 4D/Doppler, Mesin Rontgen X-Ray, CT-Scan, Defibrillator (DC Shock), Syringe Pump, Infusion Pump, Bed Pasien Elektrik (ICU/Rawat Inap), Ventilator, Mesin Anastesi, Dental Chair Unit, Lampu Operasi.',
                    subRincian: 'Alat Kedokteran Umum, Radiologi, Bedah, Kebidanan/Kandungan, Gigi, Mata, THT, Fisioterapi, Anastesi.'
                },
                {
                    prefix: '1.3.2.08',
                    kib: 'KIB B',
                    nama: 'ALAT LABORATORIUM',
                    contohRs: 'Hematology Analyzer, Clinical Chemistry Analyzer, Mikroskop Binokuler Elektrik, Centrifuge Darah/Urin, Autoclave Sterilisasi Laboratorium, Inkubator Bakteri, Biosafety Cabinet.',
                    subRincian: 'Alat Lab Kimia, Alat Lab Biologi/Medis, Alat Lab Patologi Anatomi/Klinik.'
                },
                {
                    prefix: '1.3.2.09',
                    kib: 'KIB B',
                    nama: 'ALAT PERSENJATAAN',
                    contohRs: 'Senjata Pelumpuh Gas Air Mata Satpam, Borgol Khusus Pasien Gaduh Gelisah / Psikiatri, Perlengkapan Keamanan Khusus.',
                    subRincian: 'Senjata Pelumpuh, Borgol, Alat Pengamanan Fisik.'
                },
                {
                    prefix: '1.3.2.10',
                    kib: 'KIB B',
                    nama: 'KOMPUTER',
                    contohRs: 'Komputer Server Database SIMRS, PC All-in-One Kasir & Loket Pendaftaran, Laptop Dokter/Rekam Medis, Printer Resep / Label Barcode NIBAR, Scanner Dokumen BPJS, Switch & Router Jaringan LAN.',
                    subRincian: 'Komputer PC/Unit, Laptop/Notebook, Peralatan Jaringan, Printer, Scanner, UPS Server.'
                },
                {
                    prefix: '1.3.2.11 s/d 1.3.2.19',
                    kib: 'KIB B',
                    nama: 'ALAT EKSPLORASI S/D OLAH RAGA',
                    contohRs: 'Phantom / Boneka Manekin CPR Pelatihan Medis Diklat, Alat Treadmill Uji Jantung / Fisioterapi, Matras Senam Hamil, Peralatan Olah Raga Karyawan.',
                    subRincian: 'Alat Peraga Medis, Alat Olah Raga, Alat Keselamatan Kerja.'
                },
                {
                    prefix: '1.3.3.01',
                    kib: 'KIB C',
                    nama: 'BANGUNAN GEDUNG',
                    contohRs: 'Gedung IGD, Gedung Poliklinik Terpadu, Gedung Rawat Inap / Paviliun, Bangunan Instalasi Farmasi, Selasar Penghubung Ruangan, Pos Satpam, Musholla RS, Pagar Keliling RS.',
                    subRincian: 'Bangunan Gedung Tempat Kerja, Bangunan Fasilitas Kesehatan, Pos Keamanan, Pagar.'
                },
                {
                    prefix: '1.3.4.01 s/d 1.3.4.04',
                    kib: 'KIB D',
                    nama: 'JALAN, IRIGASI DAN JARINGAN',
                    contohRs: 'Jalan Aspal Akses Masuk IGD/Ambulans, Saluran Drainase Air Hujan RS, Instalasi Pengolahan Air Limbah (IPAL), Jaringan Pipa Gas Oksigen Sentral Medis, Jaringan Kabel Fiber Optik SIMRS.',
                    subRincian: 'Jalan dan Jembatan, Bangunan Air/Drainase, Instalasi Listrik/Pipa Medis, Jaringan Komunikasi/Data.'
                },
                {
                    prefix: '1.3.5.01',
                    kib: 'KIB E',
                    nama: 'BUKU / KEPUSTAKAAN',
                    contohRs: 'Buku Referensi Medis Kedokteran, Buku Pedoman Farmakope, Jurnal Ilmiah Akreditasi RS, Buku Pedoman Standar Pelayanan Minimal (SPM).',
                    subRincian: 'Buku Umum, Buku Khusus Medis, Terbitan Berkala/Jurnal.'
                },
                {
                    prefix: '1.3.5.02',
                    kib: 'KIB E',
                    nama: 'BARANG BERCORAK KESENIAN',
                    contohRs: 'Lukisan Hiasan Dinding Ruang Rapat Direksi/Auditorium, Patung Dekorasi Taman Rumah Sakit, Ornamen Kesenian Daerah.',
                    subRincian: 'Barang Kesenian Daerah, Seni Rupa, Lukisan.'
                },
                {
                    prefix: '1.3.5.03',
                    kib: 'KIB E',
                    nama: 'HEWAN',
                    contohRs: 'Tikus/Mencit Putih untuk Uji Coba Laboratorium Farmasi/Medis, Anjing Pelacak Pengamanan Wilayah Rumah Sakit.',
                    subRincian: 'Hewan Pengaman, Hewan Laboratorium, Hewan Ternak.'
                },
                {
                    prefix: '1.3.5.04 s/d 1.3.5.07',
                    kib: 'KIB E',
                    nama: 'BIOTA, TANAMAN & RENOVASI',
                    contohRs: 'Pohon Peneduh & Tanaman Hias Taman Rumah Sakit, Biota Akuarium Terapi Pasien, Aset Renovasi pada Bangunan Sewa/Bukan Milik Sendiri.',
                    subRincian: 'Biota Perairan, Tanaman Hias, Aset Tetap Renovasi.'
                },
                {
                    prefix: '1.3.6.01',
                    kib: 'KIB F',
                    nama: 'KONSTRUKSI DALAM PENGERJAAN (KDP)',
                    contohRs: 'Proyek Pembangunan Gedung Baru / Ruang Operasi yang saat ini masih dalam proses pekerjaan fisik dan belum 100% rampung (belum terbit BAST selesai).',
                    subRincian: 'KDP Gedung dan Bangunan, KDP Jalan/Instalasi.'
                },
                {
                    prefix: '1.5.1 s/d 1.5.4',
                    kib: 'ASET LAINNYA',
                    nama: 'ASET TIDAK BERWUJUD (ATB) & ASET LAIN',
                    contohRs: 'Lisensi Software SIMRS (Sistem Informasi Manajemen Rumah Sakit), Lisensi Database Oracle / Windows Server, Hak Cipta Aplikasi Antrian Online, Aset Rusak Berat Menunggu SK Penghapusan BPKAD.',
                    subRincian: 'Aset Tidak Berwujud (ATB), Kemitraan Pihak Ketiga, Aset Lain-Lain.'
                },
                {
                    prefix: 'KOREKSI',
                    kib: 'KOREKSI',
                    nama: 'KOREKSI & PENYEIMBANG',
                    contohRs: 'Koreksi Hibah Mesin Hemodialisa Kemenkes, Pengalihan barang nilai di bawah kapitalisasi (< Rp 300.000 / Rp 500.000) ke Ekstrakomptabel, Koreksi Audit BPK.',
                    subRincian: 'Koreksi Hibah, Koreksi Ekstrakomptabel, Koreksi Lain-Lain.'
                }
            ],

            formData: {
                astap_id: '',
                nama_barang: '',
                jenis_reklas: 'KOREKSI_REKENING',
                jenis_reklasifikasi_asal_id: '',
                jenis_reklasifikasi_tujuan_id: '',
                nilai_reklas: 0,
                tanggal_reklas: new Date().toISOString().split('T')[0],
                triwulan: {{ $selectedTw === 'all' ? 1 : (int)$selectedTw }},
                tahun: {{ $selectedTahun }},
                nomor_ba_reklas: '',
                keterangan: '',
            },

            openModalTambah() {
                this.formData = {
                    astap_id: '',
                    nama_barang: '',
                    jenis_reklas: 'KOREKSI_REKENING',
                    jenis_reklasifikasi_asal_id: '',
                    jenis_reklasifikasi_tujuan_id: '',
                    nilai_reklas: 0,
                    tanggal_reklas: new Date().toISOString().split('T')[0],
                    triwulan: {{ $selectedTw === 'all' ? 1 : (int)$selectedTw }},
                    tahun: {{ $selectedTahun }},
                    nomor_ba_reklas: '',
                    keterangan: '',
                };
                this.showModalTambah = true;
            },

            onSelectAstap(astapId) {
                if (!astapId) return;
                const selectEl = document.querySelector(`select[x-model="formData.astap_id"]`);
                const option = selectEl ? selectEl.querySelector(`option[value="${astapId}"]`) : null;
                if (!option) return;

                const nilai = parseFloat(option.getAttribute('data-nilai') || 0);
                const nama = option.getAttribute('data-nama') || '';
                const prefix = option.getAttribute('data-prefix') || '';

                this.formData.nilai_reklas = nilai;
                this.formData.nama_barang = nama;

                // Auto-pilih baris asal berdasarkan prefix kode
                if (prefix) {
                    const asalSelect = document.querySelector(`select[x-model="formData.jenis_reklasifikasi_asal_id"]`);
                    if (asalSelect) {
                        for (let opt of asalSelect.options) {
                            if (opt.text.includes(prefix)) {
                                this.formData.jenis_reklasifikasi_asal_id = opt.value;
                                break;
                            }
                        }
                    }
                }
            },

            getNarasiPreview() {
                const nama = this.formData.nama_barang || 'Aset Terpilih';
                const nilai = new Intl.NumberFormat('id-ID').format(this.formData.nilai_reklas || 0);
                const noBa = this.formData.nomor_ba_reklas ? ` dengan Nomor BA: ${this.formData.nomor_ba_reklas}` : '';
                
                switch (this.formData.jenis_reklas) {
                    case 'KOREKSI_REKENING':
                        return `Telah dilakukan koreksi rekening belanja / reklasifikasi internal atas barang "${nama}" senilai Rp ${nilai}${noBa} karena adanya penyesuaian klasifikasi sub-rincian objek PMDN 108.`;
                    case 'KDP_TO_DEFINITIF':
                        return `Telah diselesaikan konstruksi fisik / KDP atas aset "${nama}" senilai Rp ${nilai}${noBa} dan direklasifikasi menjadi aset tetap definitif (Gedung dan Bangunan).`;
                    case 'EKSTRAKOMPTABEL':
                        return `Telah dilakukan koreksi ekstrakomptabel atas aset "${nama}" senilai Rp ${nilai}${noBa} karena nilai perolehannya berada di bawah batas kapitalisasi aset tetap RSUD Dr. H. Koesnandi.`;
                    case 'HIBAH_MASUK':
                        return `Telah dicatat penambahan aset tetap melalui reklasifikasi hibah/bantuan pemerintah atas barang "${nama}" senilai Rp ${nilai}${noBa}.`;
                    default:
                        return `Telah dilakukan reklasifikasi aset tetap atas barang "${nama}" senilai Rp ${nilai}${noBa}.`;
                }
            },

            async submitFormTambah() {
                if (this.isSubmitting) return;
                this.isSubmitting = true;

                try {
                    const response = await fetch('{{ route("master.reklasifikasi.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(this.formData),
                    });

                    const result = await response.json();

                    if (result.success) {
                        alert('✅ ' + result.message);
                        window.location.reload();
                    } else {
                        alert('❌ ' + (result.message || 'Gagal menyimpan data reklasifikasi.'));
                    }
                } catch (error) {
                    alert('❌ Terjadi kesalahan jaringan saat menyimpan data: ' + error.message);
                } finally {
                    this.isSubmitting = false;
                }
            },

            confirmHapusReklas(id, nama) {
                this.deleteTargetId = id;
                this.deleteTargetName = nama;
                this.showConfirmDelete = true;
            },

            async executeHapusReklas() {
                if (!this.deleteTargetId) return;

                try {
                    const url = `{{ url('/master-data/reklasifikasi') }}/${this.deleteTargetId}`;
                    const response = await fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    });

                    const result = await response.json();

                    if (result.success) {
                        alert('✅ ' + result.message);
                        window.location.reload();
                    } else {
                        alert('❌ ' + (result.message || 'Gagal menghapus transaksi.'));
                    }
                } catch (error) {
                    alert('❌ Terjadi kesalahan saat menghapus transaksi: ' + error.message);
                } finally {
                    this.showConfirmDelete = false;
                }
            },

            exportToExcel() {
                if (typeof XLSX === 'undefined') {
                    alert('⚠️ Pustaka Excel sedang dimuat. Silakan coba kembali sesaat lagi.');
                    return;
                }

                const table = document.querySelector('table');
                if (!table) {
                    alert('⚠️ Tabel tidak ditemukan.');
                    return;
                }

                const wb = XLSX.utils.book_new();
                const ws = XLSX.utils.table_to_sheet(table);
                XLSX.utils.book_append_sheet(wb, ws, "Sheet3_REKLAS");

                const filename = `REKLAS_ASET_RSUD_KOESNANDI_{{ $selectedTahun }}_TW{{ $selectedTw }}.xlsx`;
                XLSX.writeFile(wb, filename);
            }
        };
    }
</script>
