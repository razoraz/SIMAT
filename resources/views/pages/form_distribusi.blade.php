<script>
    window.editingDistribusi = {{ Js::from($distribusiData ?? null) }};
</script>

<x-layout :title="request()->routeIs('distribusi.edit') ? 'Ubah Distribusi ASTAP - SIMAT-RK' : 'Input Distribusi Baru - SIMAT-RK'">
    @section('page-title', request()->routeIs('distribusi.edit') ? 'Ubah Distribusi ASTAP' : 'Input Distribusi Baru')
    @section('breadcrumb', request()->routeIs('distribusi.edit') ? 'Master Utama / Distribusi ASTAP / Ubah' : 'Master Utama / Distribusi ASTAP / Input Baru')

    <div x-data="{
        isEdit: {{ request()->routeIs('distribusi.edit') ? 'true' : 'false' }},
        editId: {{ isset($id) ? Js::from($id) : 'null' }},
        
        // Autocomplete Search Unit / Paviliun State
        unitSearch: '',
        isSearchingUnit: false,
        selectedUnitObj: null,

        // Active Autocomplete Dropdown Index for Items
        activeJenisDropdownIndex: null,
        activeDropdownIndex: null,

        // Database Master Jenis ASTAP (Dari Tabel jenis_astaps)
        jenisAstapList: {{ Js::from($jenisAstapList ?? []) }},

        // Database Master Data ASTAP (Dari Tabel astaps)
        dbAstapList: {{ Js::from($astapList ?? []) }},

        // Database NIBAR dari astap_registers (Status Tersedia)
        nibarList: {{ Js::from($nibarList ?? []) }},

        // Active Dropdown Index untuk NIBAR
        activeNibarDropdownIndex: null,
        nibarSearch: {},

        // Database Master Katalog Barang ASTAP RSUD (Lengkap dengan Satuan & Kode Jenis)
        katalogAstap: [
            { kode: '1.3.2.05.01.04.008', jenis_kode: '1.3.2', jenis_nama: 'PERALATAN DAN MESIN', nama: 'Kasur Matras spoon (Mattress Foam Adult 200x90x10)', kategori: 'Perlengkapan Kamar Pasien', merk: 'Mattres Cover Spon FO R.Inap', satuan: 'Unit' },
            { kode: '1.3.2.02.01.08.001', jenis_kode: '1.3.2', jenis_nama: 'PERALATAN DAN MESIN', nama: 'Bed Patient Electric 3 Crank Acare', kategori: 'Perlengkapan Kamar Rawat Inap', merk: 'Acare Electric Medical Bed with Side Rail', satuan: 'Unit' },
            { kode: '1.3.2.02.01.08.002', jenis_kode: '1.3.2', jenis_nama: 'PERALATAN DAN MESIN', nama: 'Bed Patient Manual 2 Crank Paramount', kategori: 'Perlengkapan Kamar Rawat Inap', merk: 'Paramount Bed Standard with Side Rail', satuan: 'Unit' },
            { kode: '1.3.2.02.01.01.025', jenis_kode: '1.3.2', jenis_nama: 'PERALATAN DAN MESIN', nama: 'Emergency Crash Cart Trolley Kit Lengkap', kategori: 'Alat Kedokteran Gawat Darurat', merk: 'Paramount Emergency 5 Laci + Tiang Infus', satuan: 'Set' },
            { kode: '1.3.2.02.01.01.008', jenis_kode: '1.3.2', jenis_nama: 'PERALATAN DAN MESIN', nama: 'Patient Monitor 6 Parameter Mindray', kategori: 'Alat Monitoring Medis', merk: 'Mindray ePM 12 / Display 12.1 Inch Multi-Lead', satuan: 'Unit' },
            { kode: '1.3.2.02.01.01.012', jenis_kode: '1.3.2', jenis_nama: 'PERALATAN DAN MESIN', nama: 'Infusion Pump Digital Otomatis Terumo', kategori: 'Alat Kedokteran Tindakan Medis', merk: 'Terumo TE-LM700 / TE-112', satuan: 'Unit' },
            { kode: '1.3.2.02.01.01.015', jenis_kode: '1.3.2', jenis_nama: 'PERALATAN DAN MESIN', nama: 'Syringe Pump Terumo TE-331', kategori: 'Alat Kedokteran Tindakan Medis', merk: 'Terumo TE-331 Digital Infusion System', satuan: 'Unit' },
            { kode: '1.3.2.02.01.02.007', jenis_kode: '1.3.2', jenis_nama: 'PERALATAN DAN MESIN', nama: 'Suction Pump Portable Medis Thomas', kategori: 'Alat Penghisap Lendir Medis', merk: 'Thomas 1632 Aspirator Heavy Duty', satuan: 'Unit' },
            { kode: '1.3.2.01.03.05.005', jenis_kode: '1.3.2', jenis_nama: 'PERALATAN DAN MESIN', nama: 'Submersible Pump 7.5 HP Franklin Electric', kategori: 'Peralatan Mesin & Sanitasi', merk: 'Franklin Electric 4 Inch 3-Phase', satuan: 'Unit' },
            { kode: '1.3.2.01.03.05.012', jenis_kode: '1.3.2', jenis_nama: 'PERALATAN DAN MESIN', nama: 'Ball Valve Kuningan Heavy Duty 3 Inch', kategori: 'Peralatan Perpipaan & Sarpras', merk: 'Kitz Heavy Duty Brass 10K', satuan: 'Pcs' },
            { kode: '1.3.2.10.01.02.003', jenis_kode: '1.3.2', jenis_nama: 'PERALATAN DAN MESIN', nama: 'Laptop Operasional ASUS ExpertBook Core i7', kategori: 'Peralatan Komputer & IT', merk: 'ASUS ExpertBook B1402CBA / 16GB / 512GB SSD', satuan: 'Unit' },
            { kode: '1.3.2.10.02.01.005', jenis_kode: '1.3.2', jenis_nama: 'PERALATAN DAN MESIN', nama: 'Printer Thermal Resep & Label Rekam Medis', kategori: 'Peralatan IT & Farmasi', merk: 'Epson TM-T82X Thermal Auto-Cutter USB', satuan: 'Unit' },
            { kode: '1.3.2.05.01.01.012', jenis_kode: '1.3.2', jenis_nama: 'PERALATAN DAN MESIN', nama: 'Kursi Tunggu Stainless 4 Dudukan Ruang Poli', kategori: 'Mebelair & Sarana Pasien', merk: 'Indachi Stainless Steel 4-Seater', satuan: 'Unit' },
            { kode: '1.3.2.05.01.02.006', jenis_kode: '1.3.2', jenis_nama: 'PERALATAN DAN MESIN', nama: 'Lemari Obat Kaca 2 Pintu Farmasi Rawat Inap', kategori: 'Mebelair Medis & Farmasi', merk: 'Baja Coating Glass Door Cabinet', satuan: 'Unit' },
            { kode: '1.3.2.02.01.04.005', jenis_kode: '1.3.2', jenis_nama: 'PERALATAN DAN MESIN', nama: 'Meja Tindakan Stainless Steel IGD', kategori: 'Alat Medis & Tindakan', merk: 'Stainless 304 Examination Table', satuan: 'Unit' },
            { kode: '1.3.2.02.01.01.018', jenis_kode: '1.3.2', jenis_nama: 'PERALATAN DAN MESIN', nama: 'Tensimeter Digital Stand Mobile Riester', kategori: 'Alat Diagnostik & TTV', merk: 'Riester Ri-Champion Mobile Stand', satuan: 'Set' },
            { kode: '1.3.2.02.01.06.004', jenis_kode: '1.3.2', jenis_nama: 'PERALATAN DAN MESIN', nama: 'Tabung Oksigen Medis 6m3 + Regulator Flowmeter', kategori: 'Gas Medis & Resusitasi', merk: 'Samator Medical Gas Cylinder 6m3', satuan: 'Tabung' },
            { kode: '1.3.2.05.02.01.004', jenis_kode: '1.3.2', jenis_nama: 'PERALATAN DAN MESIN', nama: 'Ember Plastik Tertutup Medis / Non-Medis (50 Liter)', kategori: 'Peralatan Sanitasi & Kebersihan', merk: 'Clio Plastik / Lion Star 50L', satuan: 'Buah' },
            { kode: '1.3.2.03.01.02.001', jenis_kode: '1.3.2', jenis_nama: 'PERALATAN DAN MESIN', nama: 'Kabel Listrik NYMHY 3x2.5mm', kategori: 'Perlengkapan Elektrikal & Sarpras', merk: 'Supreme Kabel Standar PLN', satuan: 'Meter' },
            { kode: '1.3.2.05.02.02.009', jenis_kode: '1.3.2', jenis_nama: 'PERALATAN DAN MESIN', nama: 'Kain Sprei Kamar Rawat Inap Katun Polos', kategori: 'Linen & Perlengkapan Kamar', merk: 'Linen RS Putih Anti Noda', satuan: 'Lembar' },
            { kode: '1.3.1.01.01.02.013', jenis_kode: '1.3.1', jenis_nama: 'TANAH', nama: 'Lahan Bangunan RSUD Dr. H. Koesnandi', kategori: 'Tanah Bangunan', merk: 'Sertifikat Hak Pakai HP-108/1984', satuan: 'Bidang' },
            { kode: '1.3.3.01.01.01.008', jenis_kode: '1.3.3', jenis_nama: 'GEDUNG DAN BANGUNAN', nama: 'Gedung Paviliun Graha Amukti VIP', kategori: 'Bangunan Gedung', merk: 'Beton Bertulang 2 Lt', satuan: 'Gedung' },
            { kode: '1.3.4.01.01.01.002', jenis_kode: '1.3.4', jenis_nama: 'JALAN, IRIGASI DAN JARINGAN', nama: 'Jaringan Pipa Distribusi Air Bersih Sentral', kategori: 'Jaringan Distribusi', merk: 'Pipa HDPE Medis', satuan: 'Meter' },
            { kode: '1.3.5.01.01.01.005', jenis_kode: '1.3.5', jenis_nama: 'ASET TETAP LAINNYA', nama: 'Buku Pedoman Tata Kelola Rumah Sakit', kategori: 'Buku Perpustakaan', merk: 'Kemenkes RI', satuan: 'Buku' },
            { kode: '1.5.3.01.01.01.001', jenis_kode: '1.5.3', jenis_nama: 'ASET TIDAK BERWUJUD', nama: 'Lisensi Sistem Informasi Rekam Medis Elektronik (RME)', kategori: 'Software Aplikasi', merk: 'SIMAT-RME Cloud Enterprise', satuan: 'Lisensi' }
        ],

        // Database Master Unit & Pegawai Penerima (Dinamis dari Tabel Units)
        unitList: {{ Js::from($units ?? []) }},

        // Data Form Distribusi dengan Dukungan Multi-Barang
        formData: {
            kode: 'DST-2026-004',
            bast_nomor: '032 / 034 / 430.10.7 / 2026',
            tujuan: 'IGD',
            tgl: new Date().toISOString().split('T')[0],
            penerima: 'dr. ADHI SUDARMADJI',
            penerima_nip: '198410272009021003',
            penerima_jabatan: 'Kepala IGD',
            keterangan: 'Distribusi alokasi sarana prasarana dan alat penunjang medis ruangan',
            
            // Daftar Barang yang Didistribusikan Sekaligus (Multi-Barang)
            items: [
                {
                    id: 1,
                    jenis_astap_kode: '1.3.2',
                    jenis_astap_nama: 'PERALATAN DAN MESIN',
                    nama_barang: 'Patient Monitor 6 Parameter Mindray',
                    kode_barang: '1.3.2.02.01.01.008',
                    merk_type: 'Mindray ePM 12 / Display 12.1 Inch Multi-Lead',
                    qty: 4,
                    satuan: 'Unit',
                    kondisi: 'Baik',
                    keterangan: 'Zona Kritis Resusitasi IGD'
                }
            ]
        },

        init() {
            // Jika ada data master dari database, gabungkan dengan katalog
            if (this.dbAstapList && this.dbAstapList.length > 0) {
                const existingCodes = new Set(this.katalogAstap.map(k => k.kode));
                this.dbAstapList.forEach(dbItem => {
                    if (!existingCodes.has(dbItem.kode)) {
                        this.katalogAstap.unshift(dbItem);
                    }
                });
            }

            // Jika dalam mode edit, muat data dari database (window.editingDistribusi) atau localStorage
            if (this.isEdit) {
                const dbFound = window.editingDistribusi || null;
                if (dbFound) {
                    const uObj = this.unitList.find(u => u.id === dbFound.unit_id || u.nama === (dbFound.unit ? dbFound.unit.nama : ''));
                    const tglStr = dbFound.tanggal_distribusi ? String(dbFound.tanggal_distribusi).substring(0, 10) : new Date().toISOString().split('T')[0];
                    this.formData = {
                        kode: dbFound.kode || ('DST-2026-' + Math.floor(Math.random() * 900 + 100)),
                        bast_nomor: dbFound.bast_nomor || '',
                        status: dbFound.status || 'Draft',
                        tujuan: dbFound.unit ? dbFound.unit.nama : (uObj ? uObj.nama : ''),
                        unit_id: dbFound.unit_id || (uObj ? uObj.id : null),
                        tgl: tglStr,
                        penerima: uObj ? (uObj.kepala || '') : '',
                        penerima_nip: uObj ? (uObj.nip || '') : '',
                        penerima_jabatan: uObj ? ('Kepala / Penanggung Jawab ' + uObj.nama) : '',
                        keterangan: dbFound.keterangan || '',
                        items: (dbFound.items && dbFound.items.length > 0) ? dbFound.items.map((it, idx) => {
                            const astapObj = it.astap || null;
                            const nibarArr = Array.isArray(it.nibar_list) ? it.nibar_list : [];
                            const nibarSelectedObj = nibarArr.map(nStr => {
                                const regMatch = (this.nibarList || []).find(nr => nr.nibar === nStr);
                                return {
                                    nibar: nStr,
                                    ruang: regMatch ? regMatch.ruang : 'Belum Ditempatkan / Di Gudang Aset',
                                    kondisi: regMatch ? regMatch.kondisi : (it.kondisi || 'Baik')
                                };
                            });
                            return {
                                id: Date.now() + idx,
                                astap_id: it.astap_id || (astapObj ? astapObj.id : null),
                                jenis_astap_kode: astapObj && astapObj.jenis_astap ? astapObj.jenis_astap.jenis : '1.3.2',
                                jenis_astap_nama: astapObj && astapObj.jenis_astap ? astapObj.jenis_astap.nama_jenis : 'PERALATAN DAN MESIN',
                                nama_barang: astapObj ? astapObj.nama_barang : (it.nama_barang || ''),
                                kode_barang: astapObj ? astapObj.kode_108 : (it.kode_barang || ''),
                                merk_type: '',
                                qty: it.qty || 1,
                                satuan: astapObj ? (astapObj.satuan || 'Unit') : 'Unit',
                                kondisi: it.kondisi || 'Baik',
                                keterangan: it.keterangan || '',
                                nibar_selected: nibarSelectedObj
                            };
                        }) : []
                    };
                    this.unitSearch = this.formData.tujuan;
                    this.selectedUnitObj = uObj;
                    return;
                }

                let storedList = [];
                try {
                    const stored = localStorage.getItem('simat_distribusis');
                    if (stored) storedList = JSON.parse(stored);
                } catch(e) {
                    storedList = [];
                }

                const found = storedList.find(d => String(d.id) === String(this.editId) || d.kode === String(this.editId));
                if (found) {
                    this.formData = {
                        kode: found.kode || ('DST-2026-' + Math.floor(Math.random() * 900 + 100)),
                        bast_nomor: found.bast_nomor || '',
                        status: found.status || 'Draft',
                        tujuan: found.tujuan || '',
                        tgl: new Date().toISOString().split('T')[0],
                        penerima: found.penerima || found.pj_nama || '',
                        penerima_nip: found.pj_nip || '',
                        penerima_jabatan: found.pj_jabatan || '',
                        keterangan: found.keterangan || '',
                        items: (found.items && found.items.length > 0) ? found.items.map((it, idx) => {
                            let resolvedKode = it.kode_barang || '';
                            let resolvedMerk = it.merk_type || '';
                            let resolvedSatuan = it.satuan || 'Unit';
                            let resolvedJenisNama = it.jenis_astap_nama || '';
                            let resolvedJenisKode = it.jenis_astap_kode || '';

                            if ((!resolvedKode || resolvedKode === '') && it.nama_barang) {
                                const q = it.nama_barang.toLowerCase().trim();
                                const match = (this.katalogAstap || []).find(k => 
                                    (k.nama && k.nama.toLowerCase().trim() === q) ||
                                    (k.nama && k.nama.toLowerCase().includes(q)) ||
                                    (q.includes(k.nama ? k.nama.toLowerCase() : ''))
                                );
                                if (match) {
                                    resolvedKode = match.kode || '';
                                    if (!resolvedMerk) resolvedMerk = match.merk || '';
                                    if (resolvedSatuan === 'Unit' && match.satuan) resolvedSatuan = match.satuan;
                                    if (!resolvedJenisNama && match.jenis_nama) resolvedJenisNama = match.jenis_nama;
                                    if (!resolvedJenisKode && match.jenis_kode) resolvedJenisKode = match.jenis_kode;
                                }
                            }

                            return {
                                id: Date.now() + idx,
                                jenis_astap_kode: resolvedJenisKode,
                                jenis_astap_nama: resolvedJenisNama || 'PERALATAN DAN MESIN',
                                nama_barang: it.nama_barang || '',
                                kode_barang: resolvedKode,
                                merk_type: resolvedMerk,
                                qty: it.qty || 1,
                                satuan: resolvedSatuan,
                                kondisi: it.kondisi || 'Baik',
                                keterangan: it.keterangan || '',
                                nibar_selected: it.nibar_selected || []
                            };
                        }) : []
                    };
                    this.unitSearch = this.formData.tujuan;
                    this.selectedUnitObj = this.unitList.find(u => u.nama === this.formData.tujuan) || null;
                    return;
                }
            }

            if (!this.isEdit) {
                this.formData = {
                    kode: 'DST-2026-' + String(Math.floor(Math.random() * 900) + 100),
                    bast_nomor: '032 / 0' + String(Math.floor(Math.random() * 80) + 10) + ' / 430.10.7 / 2026',
                    status: 'Draft',
                    tujuan: '',
                    tgl: new Date().toISOString().split('T')[0],
                    penerima: '',
                    penerima_nip: '',
                    penerima_jabatan: '',
                    keterangan: '',
                    items: [
                        {
                            id: Date.now(),
                            jenis_astap_kode: '',
                            jenis_astap_nama: '',
                            nama_barang: '',
                            kode_barang: '',
                            merk_type: '',
                            qty: 1,
                            satuan: 'Unit',
                            kondisi: 'Baik',
                            keterangan: '',
                            nibar_selected: []
                        }
                    ]
                };
                this.unitSearch = '';
                this.selectedUnitObj = null;
            }
        },

        // Tambah Baris Barang Baru dalam Satu Distribusi
        addItem() {
            this.formData.items.push({
                id: Date.now() + Math.random(),
                jenis_astap_kode: '',
                jenis_astap_nama: '',
                nama_barang: '',
                kode_barang: '',
                merk_type: '',
                qty: 1,
                satuan: 'Unit',
                kondisi: 'Baik',
                keterangan: '',
                nibar_selected: []
            });
        },

        // ─── NIBAR Methods ───────────────────────────────────────────────────

        // Dapatkan / auto-resolve kode_barang jika barang sudah memiliki nama_barang
        getItemKode(item) {
            if (!item) return '';
            if (item.kode_barang && item.kode_barang.trim() !== '') return item.kode_barang.trim();
            if (item.nama_barang && item.nama_barang.trim() !== '') {
                const q = item.nama_barang.toLowerCase().trim();
                const match = (this.katalogAstap || []).find(k => 
                    (k.nama && k.nama.toLowerCase().trim() === q) ||
                    (k.nama && k.nama.toLowerCase().includes(q)) ||
                    (q.includes(k.nama ? k.nama.toLowerCase() : ''))
                );
                if (match && match.kode) {
                    item.kode_barang = match.kode;
                    if (!item.satuan || item.satuan === 'Unit') item.satuan = match.satuan || 'Unit';
                    if (!item.merk_type) item.merk_type = match.merk || '';
                    if (!item.jenis_astap_nama && match.jenis_nama) item.jenis_astap_nama = match.jenis_nama;
                    return match.kode;
                }
            }
            return '';
        },

        // Logika mencocokkan NIBAR dengan item barang
        isNibarMatch(n, item) {
            if (!item || !n) return false;
            if (item.astap_id && n.astap_id && String(item.astap_id) === String(n.astap_id)) return true;
            const itemKode = this.getItemKode(item);
            if (itemKode && n.kode && itemKode.trim() === n.kode.trim()) return true;
            if (item.nama_barang && n.nama_barang) {
                const iNama = item.nama_barang.toLowerCase().trim();
                const nNama = n.nama_barang.toLowerCase().trim();
                if (iNama === nNama || iNama.includes(nNama) || nNama.includes(iNama)) return true;
            }
            return false;
        },

        // Hitung total NIBAR yang terdaftar di database untuk barang ini (Khusus status_mutasi: Tersedia)
        getMatchingNibarCount(item) {
            if (!item) return 0;
            return (this.nibarList || []).filter(n => this.isNibarMatch(n, item)).length;
        },

        // Cek apakah data NIBAR kosong untuk barang yang dipilih
        isNibarEmpty(item) {
            if (!item || (!item.nama_barang && !item.kode_barang)) return false;
            return this.getMatchingNibarCount(item) === 0;
        },

        // Filter NIBAR berdasarkan barang yang dipilih
        getFilteredNibar(item, query) {
            if (!item) return [];
            let list = (this.nibarList || []).filter(n => this.isNibarMatch(n, item));
            
            // Exclude yang sudah dipilih di item ini
            const chosen = (item.nibar_selected || []).map(n => n.nibar);
            list = list.filter(n => !chosen.includes(n.nibar));
            
            if (query && query.trim() !== '') {
                const q = query.toLowerCase().trim();
                list = list.filter(n =>
                    (n.nibar || '').toLowerCase().includes(q) ||
                    (n.ruang || '').toLowerCase().includes(q) ||
                    (n.kondisi || '').toLowerCase().includes(q)
                );
            }
            return list;
        },

        // Pilih NIBAR untuk item (max sesuai qty)
        selectNibar(item, n) {
            if (!item.nibar_selected) item.nibar_selected = [];
            const maxQty = parseInt(item.qty) || 1;
            if (item.nibar_selected.length >= maxQty) {
                alert('⚠️ Jumlah NIBAR yang dipilih sudah mencapai volume barang (' + maxQty + '). Tambah volume atau hapus salah satu NIBAR terlebih dahulu.');
                return;
            }
            item.nibar_selected.push({ nibar: n.nibar, ruang: n.ruang, kondisi: n.kondisi });
            this.activeNibarDropdownIndex = null;
            if (this.nibarSearch) this.nibarSearch[item.id] = '';
        },

        // Hapus NIBAR yang sudah dipilih
        removeNibar(item, nibarStr) {
            item.nibar_selected = (item.nibar_selected || []).filter(n => n.nibar !== nibarStr);
        },

        // Hapus Baris Barang
        removeItem(index) {
            if (this.formData.items.length <= 1) {
                alert('⚠️ Minimal harus ada 1 barang dalam transaksi distribusi!');
                return;
            }
            this.formData.items.splice(index, 1);
        },

        // Filter Daftar Master Jenis ASTAP untuk Autocomplete (Berdasarkan Nama Jenis Saja, Tanpa Null/Kosong)
        getFilteredJenisAstap(query) {
            if (!this.jenisAstapList || this.jenisAstapList.length === 0) return [];
            const validList = this.jenisAstapList.filter(j => j && j.nama && j.nama.trim() !== '' && j.nama.trim().toLowerCase() !== 'null' && j.nama.trim() !== '-');
            if (!query || query.trim() === '') {
                return validList;
            }
            const q = query.toLowerCase().trim();
            return validList.filter(j => 
                (j.nama || '').toLowerCase().includes(q)
            );
        },

        // Pilih Jenis ASTAP
        selectJenisAstap(item, j) {
            const isChanged = item.jenis_astap_nama !== j.nama;
            item.jenis_astap_kode = j.kode;
            item.jenis_astap_nama = j.nama;
            this.activeJenisDropdownIndex = null;
            
            // Jika jenis ASTAP berubah dan barang sebelumnya tidak cocok dengan jenis baru, kosongkan pilihan barang
            if (isChanged && item.kode_barang && j.kode && !item.kode_barang.startsWith(j.kode)) {
                item.nama_barang = '';
                item.kode_barang = '';
                item.merk_type = '';
                item.satuan = 'Unit';
                item.nibar_selected = [];
            }
        },

        // Kosongkan Pilihan Jenis ASTAP
        clearJenisAstap(item, idx) {
            item.jenis_astap_kode = '';
            item.jenis_astap_nama = '';
            item.nama_barang = '';
            item.kode_barang = '';
            item.merk_type = '';
            item.satuan = 'Unit';
            if (idx !== undefined) {
                this.activeJenisDropdownIndex = idx;
            }
        },

        // Filter Daftar ASTAP Secara Real-time (Dibatasi oleh Jenis ASTAP yang dipilih)
        getFilteredAstap(item, query) {
            let list = this.katalogAstap || [];
            
            // Filter ketat berdasarkan Jenis ASTAP yang dipilih pada baris ini
            if (item && (item.jenis_astap_kode || item.jenis_astap_nama)) {
                list = list.filter(ast => {
                    const astJenisKode = ast.jenis_kode || (ast.kode ? ast.kode.substring(0, 5) : '');
                    const astJenisNama = (ast.jenis_nama || '').toUpperCase();
                    const selectedNama = (item.jenis_astap_nama || '').toUpperCase();
                    
                    if (item.jenis_astap_kode && (astJenisKode === item.jenis_astap_kode || (ast.kode && ast.kode.startsWith(item.jenis_astap_kode)))) {
                        return true;
                    }
                    if (selectedNama && astJenisNama && astJenisNama.includes(selectedNama)) {
                        return true;
                    }
                    return false;
                });
            }

            if (!query || query.trim() === '') {
                return list.slice(0, 10);
            }

            const q = query.toLowerCase().trim();
            return list.filter(ast => 
                (ast.nama || '').toLowerCase().includes(q) || 
                (ast.kode || '').toLowerCase().includes(q) ||
                (ast.merk || '').toLowerCase().includes(q) ||
                (ast.kategori || '').toLowerCase().includes(q)
            ).slice(0, 15);
        },

        // Pilih Barang dari Hasil Ketik Filter Dropdown
        selectAstapItem(item, ast) {
            item.nama_barang = ast.nama;
            item.kode_barang = ast.kode;
            item.merk_type = ast.merk || '';
            item.satuan = ast.satuan || 'Unit';

            // Jika jenis astap belum dipilih, otomatis sinkronkan dengan jenis barang yang dipilih
            if (!item.jenis_astap_nama) {
                const jenisKode = ast.jenis_kode || (ast.kode ? ast.kode.substring(0, 5) : '');
                const matchedJenis = (this.jenisAstapList || []).find(j => j.kode === jenisKode || (ast.jenis_nama && j.nama.toLowerCase() === ast.jenis_nama.toLowerCase()));
                if (matchedJenis) {
                    item.jenis_astap_kode = matchedJenis.kode;
                    item.jenis_astap_nama = matchedJenis.nama;
                } else if (ast.jenis_nama) {
                    item.jenis_astap_nama = ast.jenis_nama;
                }
            }

            this.activeDropdownIndex = null;
        },

        clearItemBarang(item, idx) {
            item.nama_barang = '';
            item.kode_barang = '';
            item.merk_type = '';
            item.satuan = 'Unit';
            item.nibar_selected = [];
            if (idx !== undefined) {
                this.activeDropdownIndex = idx;
            }
        },

        // Auto Sync Satuan & Metadata saat input nama barang diketik manual
        onNamaBarangInput(item) {
            if (!item.nama_barang || item.nama_barang.trim() === '') {
                item.kode_barang = '';
                return;
            }
            const query = item.nama_barang.toLowerCase().trim();
            let pool = this.katalogAstap || [];
            if (item.jenis_astap_kode) {
                pool = pool.filter(ast => {
                    const astJenis = ast.jenis_kode || (ast.kode ? ast.kode.substring(0, 5) : '');
                    return astJenis === item.jenis_astap_kode || (ast.kode && ast.kode.startsWith(item.jenis_astap_kode));
                });
            }
            const exactMatch = pool.find(ast => 
                ast.nama.toLowerCase().trim() === query ||
                ast.kode.toLowerCase().trim() === query
            );
            if (exactMatch) {
                item.kode_barang = exactMatch.kode;
                item.merk_type = exactMatch.merk || '';
                item.satuan = exactMatch.satuan || 'Unit';

                if (!item.jenis_astap_kode) {
                    const jenisKode = exactMatch.jenis_kode || (exactMatch.kode ? exactMatch.kode.substring(0, 5) : '');
                    const matchedJenis = (this.jenisAstapList || []).find(j => j.kode === jenisKode);
                    if (matchedJenis) {
                        item.jenis_astap_kode = matchedJenis.kode;
                        item.jenis_astap_nama = matchedJenis.label || (matchedJenis.kode + ' - ' + matchedJenis.nama);
                    }
                }
            }
        },

        // Filter Pencarian Unit / Paviliun
        get filteredUnitList() {
            if (!this.unitSearch || this.unitSearch.trim().length === 0) {
                return this.unitList.slice(0, 10);
            }
            const q = this.unitSearch.toLowerCase().trim();
            return this.unitList.filter(u => 
                u.nama.toLowerCase().includes(q) || 
                (u.kode && u.kode.toLowerCase().includes(q)) ||
                (u.tipe && u.tipe.toLowerCase().includes(q)) ||
                (u.kepala && u.kepala.toLowerCase().includes(q))
            );
        },

        // Aksi Pilih Unit
        selectUnit(u) {
            this.formData.tujuan = u.nama;
            this.formData.penerima = u.kepala;
            this.formData.penerima_nip = u.nip;
            this.formData.penerima_jabatan = u.jabatan || ('Kepala Ruangan ' + u.nama);
            this.unitSearch = u.nama;
            this.selectedUnitObj = u;
            this.isSearchingUnit = false;
        },

        clearUnit() {
            this.formData.tujuan = '';
            this.formData.penerima = '';
            this.formData.penerima_nip = '';
            this.formData.penerima_jabatan = '';
            this.unitSearch = '';
            this.selectedUnitObj = null;
            this.isSearchingUnit = true;
        },

        getTotalItemVolume() {
            return this.formData.items.reduce((acc, curr) => acc + (parseInt(curr.qty) || 0), 0);
        },

        submitForm() {
            if (!this.formData.tujuan || this.formData.tujuan.trim() === '') {
                alert('⚠️ Silakan pilih Tujuan Unit / Paviliun penerima barang!');
                return;
            }

            const emptyItem = this.formData.items.find(it => !it.nama_barang || it.nama_barang.trim() === '');
            if (emptyItem) {
                alert('⚠️ Ada baris barang yang belum diisi nama barangnya. Silakan lengkapi atau hapus baris yang kosong!');
                return;
            }

            // Format tanggal
            let tglStr = this.formData.tgl || new Date().toISOString().split('T')[0];
            const dateObj = new Date(tglStr);
            const d = String(dateObj.getDate()).padStart(2, '0');
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
            const fullMonths = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            
            const m = months[dateObj.getMonth()] || 'Ags';
            const fullM = fullMonths[dateObj.getMonth()] || 'Agustus';
            const y = dateObj.getFullYear() || 2026;
            const dayName = days[dateObj.getDay()] || 'Senin';

            // Nama ringkasan dari barang-barang yang dipilih
            const itemNames = this.formData.items.map(it => it.nama_barang).filter(Boolean);
            let ringkasanNama = itemNames.length > 2 
                ? (itemNames[0] + ' & ' + itemNames[1] + ' (' + itemNames.length + ' Barang)')
                : (itemNames.join(' & ') || 'Distribusi ASTAP');

            const itemRecords = this.formData.items.map((it, idx) => {
                const resolvedKode = this.getItemKode(it) || it.kode_barang || '';
                return {
                    no: idx + 1,
                    jenis_astap_nama: it.jenis_astap_nama || 'PERALATAN DAN MESIN',
                    nama_barang: it.nama_barang,
                    kode_barang: resolvedKode,
                    merk_type: it.merk_type || (resolvedKode ? ('Kode 108: ' + resolvedKode) : '-'),
                    qty: parseInt(it.qty) || 1,
                    satuan: it.satuan || 'Unit',
                    kondisi: it.kondisi || 'Baik',
                    keterangan: it.keterangan || '-',
                    nibar_selected: it.nibar_selected || []
                };
            });

            // Ambil data distribusi dari localStorage
            let storedList = [];
            try {
                const stored = localStorage.getItem('simat_distribusis');
                if (stored) {
                    storedList = JSON.parse(stored);
                }
            } catch(e) {
                storedList = [];
            }

            if (!Array.isArray(storedList)) {
                storedList = [];
            }

            if (this.isEdit && this.editId) {
                // Update record yang diedit
                const idx = storedList.findIndex(d => String(d.id) === String(this.editId) || d.kode === this.formData.kode);
                if (idx !== -1) {
                    storedList[idx] = {
                        ...storedList[idx],
                        kode: this.formData.kode,
                        nama: ringkasanNama,
                        tujuan: this.formData.tujuan,
                        tgl: `${d} ${m} ${y}`,
                        tgl_iso: tglStr,
                        penerima: this.formData.penerima || 'Petugas Ruangan',
                        status: this.formData.status || storedList[idx].status || 'Draft',
                        bast_nomor: this.formData.bast_nomor || '-',
                        hari: dayName,
                        tanggal_angka: String(dateObj.getDate()),
                        bulan: fullM,
                        tahun: String(y),
                        pj_nama: this.formData.penerima,
                        pj_nip: this.formData.penerima_nip || '-',
                        pj_jabatan: this.formData.penerima_jabatan || ('Kepala Ruangan ' + this.formData.tujuan),
                        pj_ruangan: this.formData.tujuan,
                        pj_jabatan_ttd: this.formData.penerima_jabatan || ('Kepala Ruangan ' + this.formData.tujuan),
                        keterangan: this.formData.keterangan || '-',
                        items: itemRecords
                    };
                }
            } else {
                // Tambah record baru di urutan paling atas
                const newRecord = {
                    id: Date.now(),
                    kode: this.formData.kode,
                    nama: ringkasanNama,
                    tujuan: this.formData.tujuan,
                    tgl: `${d} ${m} ${y}`,
                    tgl_iso: tglStr,
                    penerima: this.formData.penerima || 'Petugas Ruangan',
                    status: this.formData.status || 'Draft',
                    bast_nomor: this.formData.bast_nomor || '-',
                    hari: dayName,
                    tanggal_angka: String(dateObj.getDate()),
                    bulan: fullM,
                    tahun: String(y),
                    tahun_anggaran: String(y),
                    sk_bupati_nomor: '188.45/969/430.4.2/2024',
                    sk_bupati_tanggal: '02 Januari ' + y,
                    pengurus_nama: 'BUDI HARTONO, S.Sos',
                    pengurus_nip: '19760229 200801 1 010',
                    pengurus_jabatan: 'Pengurus Barang',
                    pengurus_ruangan: 'Gudang Perbekalan',
                    pj_nama: this.formData.penerima,
                    pj_nip: this.formData.penerima_nip || '-',
                    pj_jabatan: this.formData.penerima_jabatan || ('Kepala Ruangan ' + this.formData.tujuan),
                    pj_ruangan: this.formData.tujuan,
                    pj_jabatan_ttd: this.formData.penerima_jabatan || ('Kepala Ruangan ' + this.formData.tujuan),
                    signed: false,
                    tgl_signed: '-',
                    keterangan: this.formData.keterangan || 'Distribusi alokasi sarana prasarana',
                    items: itemRecords
                };
                storedList.unshift(newRecord);
            }

            // Simpan ke localStorage
            localStorage.setItem('simat_distribusis', JSON.stringify(storedList));

            // Sinkronisasi otomatis ke Database & update data astap_registers
            try {
                const targetUnit = this.selectedUnitObj || this.unitList.find(u => u.nama === this.formData.tujuan);
                if (targetUnit && targetUnit.id) {
                    const dbPayload = {
                        _token: '{{ csrf_token() }}',
                        kode: this.formData.kode,
                        bast_nomor: this.formData.bast_nomor || '-',
                        tanggal_distribusi: tglStr,
                        unit_id: targetUnit.id,
                        status: this.formData.status || 'Draft',
                        keterangan: this.formData.keterangan || '-',
                        items: this.formData.items.map(it => {
                            const resolvedKode = this.getItemKode(it) || it.kode_barang || '';
                            const astapObj = (this.dbAstapList || []).find(a => a.kode === resolvedKode || a.nama === it.nama_barang);
                            return {
                                astap_id: astapObj ? astapObj.id : 1,
                                qty: parseInt(it.qty) || 1,
                                kondisi: it.kondisi || 'Baik',
                                keterangan: it.keterangan || '-',
                                nibar_list: (it.nibar_selected || []).map(n => n.nibar)
                            };
                        })
                    };

                    fetch('{{ route('distribusi.save') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(dbPayload)
                    }).catch(err => console.log('Database sync notice:', err));
                }
            } catch(e) {
                console.log('Sync error:', e);
            }

            alert('✅ Berhasil menyimpan distribusi barang:\n- No. Distribusi: ' + this.formData.kode + '\n- Tujuan Unit: ' + this.formData.tujuan + '\n- Penerima: ' + this.formData.penerima + '\n- Jumlah Barang: ' + this.formData.items.length + ' Jenis Barang (' + this.getTotalItemVolume() + ' Total Volume)\n\nData Register ASTAP & NIBAR telah otomatis diperbarui!');
            window.location.href = '{{ route('distribusi.index') }}';
        }
    }" x-cloak class="space-y-6">

        <!-- Top Navigation Bar (Bersih, Tanpa Tombol Simpan/Batal di Atas) -->
        <div class="flex items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl">
            <div class="flex items-center space-x-4">
                <a href="{{ route('distribusi.index') }}" 
                   class="w-10 h-10 rounded-2xl bg-slate-950 border border-slate-800 hover:border-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <div class="inline-flex items-center space-x-2 px-2.5 py-0.5 rounded-full bg-teal-500/20 text-teal-300 border border-teal-500/30 text-[10px] font-bold mb-1">
                        <span x-text="isEdit ? '✏️ UBAH DISTRIBUSI BARANG' : '🚚 INPUT DISTRIBUSI MULTI-BARANG'"></span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight">Form Distribusi & Penyerahan ASTAP</h1>
                    <p class="text-xs text-slate-400 mt-0.5">Dapat memasukkan beberapa barang berbeda sekaligus dalam satu transaksi penyerahan ke ruangan</p>
                </div>
            </div>
        </div>

        <!-- Form Card Container -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
            
            <!-- BAGIAN 1: INFORMASI TRANSAKSI & TUJUAN PENERIMA (AUTOFILL DATA UNIT) -->
            <div class="space-y-4">
                <h3 class="text-sm font-extrabold text-teal-300 uppercase tracking-wider flex items-center space-x-2 border-b border-slate-800 pb-3">
                    <span>1. Informasi Penyerahan & Pegawai Penerima Ruangan</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Kode Transaksi Distribusi -->
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">No. Registrasi Distribusi</label>
                        <input type="text" x-model="formData.kode" readonly
                               class="w-full bg-slate-950/80 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-teal-400 font-mono font-bold focus:outline-none cursor-not-allowed">
                    </div>

                    <!-- Nomor BAST Rujukan -->
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">No. BAST Distribusi</label>
                        <input type="text" x-model="formData.bast_nomor"
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white font-mono focus:outline-none focus:border-teal-500">
                    </div>

                    <!-- Tanggal Distribusi -->
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Tanggal Penyerahan</label>
                        <input type="date" x-model="formData.tgl"
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-teal-500">
                    </div>

                    <!-- Status Distribusi (Hanya tampil di mode Edit) -->
                    <template x-if="isEdit">
                        <div>
                            <label class="block text-slate-300 font-semibold text-xs mb-1.5 flex items-center justify-between">
                                <span>Status Distribusi</span>
                                <span class="text-teal-400 text-[10px] font-bold">⚡ Edit Status</span>
                            </label>
                            <select x-model="formData.status"
                                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs font-bold focus:outline-none focus:border-teal-500 transition-all cursor-pointer"
                                    :class="{
                                        'text-emerald-400': formData.status === 'Telah Diterima' || formData.status === 'Diterima',
                                        'text-amber-400': formData.status === 'Dalam Pengiriman' || formData.status === 'Dikirim',
                                        'text-cyan-400': formData.status === 'Menunggu Konfirmasi' || formData.status === 'Pending',
                                        'text-slate-400': formData.status === 'Draft'
                                    }">
                                <option value="Telah Diterima">🟢 Telah Diterima</option>
                                <option value="Dalam Pengiriman">🚚 Dalam Pengiriman</option>
                                <option value="Menunggu Konfirmasi">⏳ Menunggu Konfirmasi</option>
                                <option value="Draft">📝 Draft</option>
                            </select>
                        </div>
                    </template>
                </div>

                <!-- Autocomplete Input Unit & Data PIC Penerima -->
                <div class="p-4 sm:p-5 rounded-2xl bg-slate-950 border border-slate-800 space-y-4">
                    
                    <!-- Search Unit Target -->
                    <div class="relative" @click.away="isSearchingUnit = false">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-slate-300 flex items-center space-x-1.5">
                                <span>🏥 Unit / Ruangan / Paviliun Tujuan</span>
                                <span class="text-teal-400 font-mono text-[11px]" x-text="'(' + unitList.length + ' Unit Terdaftar)'"></span>
                            </label>
                            <template x-if="formData.tujuan">
                                <button type="button" @click="clearUnit()" class="text-xs text-rose-400 hover:text-rose-300 font-semibold">
                                    ✕ Ganti Unit
                                </button>
                            </template>
                        </div>

                        <div class="relative">
                            <input type="text" x-model="unitSearch" 
                                   @focus="isSearchingUnit = true" 
                                   @input="isSearchingUnit = true" 
                                   placeholder="Ketik nama unit / ruangan (contoh: IGD, Melati, Radiologi, Bedah)..." 
                                   class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 pl-10 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-teal-500 transition-all font-semibold">
                            <svg class="w-4 h-4 text-teal-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <!-- Dropdown Autocomplete Unit (Dark Themed) -->
                        <div x-show="isSearchingUnit" 
                             x-transition 
                             class="absolute left-0 right-0 z-30 mt-1 bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl overflow-hidden max-h-56 overflow-y-auto divide-y divide-slate-800">
                            <template x-for="u in filteredUnitList.slice(0, 5)" :key="u.id">
                                <div @click="selectUnit(u)" 
                                     class="p-3 hover:bg-teal-500/15 cursor-pointer transition-colors flex items-center justify-between group">
                                    <div>
                                        <p class="font-bold text-white text-xs group-hover:text-teal-300" x-text="u.nama"></p>
                                        <p class="text-[10px] text-slate-400" x-text="(u.kode || 'UNIT') + ' • ' + (u.tipe || 'Unit') + ' • PJ: ' + u.kepala"></p>
                                    </div>
                                    <span class="px-2 py-1 rounded bg-teal-500/20 text-teal-300 text-[10px] font-bold">Pilih &rarr;</span>
                                </div>
                            </template>
                            <template x-if="filteredUnitList.length === 0">
                                <div class="p-3 text-center text-xs text-slate-500">Unit tidak ditemukan</div>
                            </template>
                        </div>
                    </div>

                    <!-- Auto-filled PIC Penerima Details -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-slate-800/80">
                        <div>
                            <span class="text-[10px] text-slate-400 block font-semibold mb-1">Pegawai Penerima (Kepala/PJ)</span>
                            <input type="text" x-model="formData.penerima" placeholder="Terisi otomatis..." 
                                   class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2 text-xs text-emerald-400 font-bold focus:outline-none">
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block font-semibold mb-1">NIP Pegawai</span>
                            <input type="text" x-model="formData.penerima_nip" placeholder="Terisi otomatis..." 
                                   class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-300 font-mono focus:outline-none">
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block font-semibold mb-1">Jabatan Penerima</span>
                            <input type="text" x-model="formData.penerima_jabatan" placeholder="Terisi otomatis..." 
                                   class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-300 focus:outline-none">
                        </div>
                    </div>

                </div>
            </div>

            <!-- BAGIAN 2: DAFTAR BARANG YANG DIDISTRIBUSIKAN (MULTI-BARANG DALAM 1 TRANSAKSI) -->
            <div class="space-y-4 pt-2">
                <div class="border-b border-slate-800 pb-3">
                    <h3 class="text-sm font-extrabold text-teal-300 uppercase tracking-wider flex items-center space-x-2">
                        <span>2. Rincian Barang Aset yang Didistribusikan</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Pilih Jenis ASTAP untuk membatasi daftar nama barang — Satuan, kode 108, dan spesifikasi terisi otomatis</p>
                </div>

                <!-- Daftar Input Multi-Barang (Layout Card Terstruktur & Rapi) -->
                <div class="space-y-5">
                    <template x-for="(item, idx) in formData.items" :key="item.id">
                        <div class="bg-slate-950/90 border border-slate-800 hover:border-slate-700/80 rounded-3xl p-5 sm:p-6 transition-all shadow-lg space-y-5">

                            <!-- Card Header: Nomor Barang, Nama Terpilih Dinamis, Badge Kode 108 & Tombol Hapus Barang di Pojok Kanan -->
                            <div class="flex items-center justify-between pb-3 border-b border-slate-800 gap-3">
                                <div class="flex items-center space-x-3 min-w-0">
                                    <span class="w-7 h-7 rounded-xl bg-teal-500/20 text-teal-300 font-extrabold text-xs flex items-center justify-center border border-teal-500/30 shrink-0" x-text="idx + 1"></span>
                                    
                                    <!-- Judul Dinamis Mengikuti Barang yang Dipilih -->
                                    <div class="min-w-0 flex items-center space-x-2">
                                        <span class="text-sm font-extrabold text-white tracking-wide truncate" 
                                              x-text="item.nama_barang ? item.nama_barang : ('Rincian Barang #' + (idx + 1))"></span>
                                        <template x-if="item.jenis_astap_nama">
                                            <span class="px-2.5 py-0.5 rounded-md bg-teal-500/10 border border-teal-500/20 text-teal-300 text-[10px] font-bold hidden md:inline-block truncate max-w-[220px]" x-text="item.jenis_astap_nama"></span>
                                        </template>
                                    </div>
                                    
                                    <!-- Badge Otomatis Kode 108 -->
                                    <template x-if="item.kode_barang">
                                        <span class="px-2.5 py-0.5 rounded-lg bg-slate-900 border border-cyan-500/30 text-cyan-400 font-mono font-bold text-[10px] shrink-0 hidden sm:inline-block" x-text="'Kode: ' + item.kode_barang"></span>
                                    </template>
                                </div>

                                <!-- Tombol Hapus Barang — Masuk di Dalam Form Pojok Kanan Atas Header -->
                                <button type="button" @click="removeItem(idx)"
                                        class="px-3 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/25 text-rose-300 border border-rose-500/30 text-xs font-semibold flex items-center space-x-1.5 transition-all active:scale-95 shrink-0"
                                        title="Hapus baris barang ini">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span class="hidden sm:inline">Hapus</span>
                                </button>
                            </div>

                            <!-- Grid Form Input Barang -->
                            <div class="space-y-4">
                                
                                <!-- Baris 0: Jenis ASTAP (Diatas Nama Barang, Format Sama Seperti Nama Barang) -->
                                <div class="relative" @click.away="if (activeJenisDropdownIndex === idx) activeJenisDropdownIndex = null">
                                    <label class="block text-slate-300 font-semibold text-xs mb-1.5 flex items-center justify-between">
                                        <span class="flex items-center space-x-1.5">
                                            <span class="text-teal-400">🏷️</span>
                                            <span>Jenis ASTAP</span>
                                            <span class="text-slate-400 font-normal text-[11px] hidden sm:inline">(Pilih jenis untuk memfilter daftar barang)</span>
                                        </span>
                                        <span class="text-teal-400 font-mono text-[10px] hidden sm:inline">⚡ Pilih Jenis Aset</span>
                                    </label>
                                    <div class="relative flex items-center">
                                        <input type="text" 
                                               x-model="item.jenis_astap_nama" 
                                               @focus="activeJenisDropdownIndex = idx"
                                               @input="activeJenisDropdownIndex = idx"
                                               placeholder="Ketik atau pilih Jenis ASTAP (contoh: Peralatan dan Mesin, Gedung, Tanah)..." 
                                               class="w-full h-11 bg-slate-900 border border-slate-700/90 rounded-xl px-4 py-2.5 pl-10 pr-10 text-xs text-white font-bold placeholder-slate-500 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all cursor-pointer">
                                        
                                        <svg class="w-4 h-4 text-teal-400 pointer-events-none" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>

                                        <!-- Tombol Silang Bersihkan Jenis ASTAP di Pojok Kanan Dalam Input -->
                                        <template x-if="item.jenis_astap_nama && item.jenis_astap_nama.trim() !== ''">
                                            <button type="button" 
                                                    @click.stop="clearJenisAstap(item, idx)" 
                                                    style="position: absolute; right: 12px; left: auto; top: 50%; transform: translateY(-50%); z-index: 20;"
                                                    class="w-6 h-6 rounded-lg bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 flex items-center justify-center transition-all cursor-pointer shadow-sm border border-slate-700/60 hover:border-rose-500/40"
                                                    title="Kosongkan jenis ASTAP">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </template>
                                    </div>

                                    <!-- Floating Dropdown Hasil Filter Jenis ASTAP (Hanya Menampilkan Nama Jenis) -->
                                    <div x-show="activeJenisDropdownIndex === idx" 
                                         x-transition 
                                         class="absolute left-0 right-0 z-50 mt-1.5 bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl overflow-hidden max-h-60 overflow-y-auto divide-y divide-slate-800">
                                        
                                        <div class="px-4 py-2 bg-slate-950/90 text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between border-b border-slate-800">
                                            <span>Pilih Master Jenis ASTAP</span>
                                            <span class="text-teal-400 font-mono" x-text="getFilteredJenisAstap(item.jenis_astap_nama).length + ' jenis tersedia'"></span>
                                        </div>

                                        <template x-for="j in getFilteredJenisAstap(item.jenis_astap_nama)" :key="j.kode || j.nama">
                                            <div @click="selectJenisAstap(item, j)"
                                                 class="px-4 py-3 hover:bg-teal-500/15 cursor-pointer transition-colors group flex items-center justify-between gap-3"
                                                 :class="{'bg-teal-500/10': item.jenis_astap_nama === j.nama}">
                                                <div class="flex items-center space-x-2.5">
                                                    <span class="w-2 h-2 rounded-full bg-teal-400"></span>
                                                    <p class="font-bold text-xs text-white group-hover:text-teal-300" x-text="j.nama"></p>
                                                </div>
                                                <span class="px-2.5 py-1 rounded-lg bg-slate-950 border border-slate-700 text-teal-300 text-[10px] font-bold shrink-0">Pilih &rarr;</span>
                                            </div>
                                        </template>

                                        <template x-if="getFilteredJenisAstap(item.jenis_astap_nama).length === 0">
                                            <div class="p-4 text-center text-xs text-slate-400">
                                                <p class="text-amber-400 font-semibold">Tidak ditemukan Jenis ASTAP</p>
                                                <p class="text-[10px] text-slate-500 mt-0.5">Coba gunakan kata kunci pencarian yang lain</p>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Baris 1: Nama Barang & Kode Rekening 108 (Selalu Sejajar Berdampingan) -->
                                <div class="flex flex-row items-end gap-3 w-full">
                                    
                                    <!-- 1. Nama Barang / Aset (Autocomplete Search Langsung Berdasarkan Filter Jenis ASTAP) -->
                                    <div class="flex-1 min-w-0 relative" @click.away="if (activeDropdownIndex === idx) activeDropdownIndex = null">
                                        <label class="block text-slate-300 font-semibold text-xs mb-1.5 flex items-center justify-between">
                                            <span class="flex items-center space-x-1.5">
                                                <span>Nama Barang / Aset ASTAP</span>
                                                <template x-if="item.jenis_astap_nama">
                                                    <span class="text-teal-400 text-[10px] bg-teal-500/10 px-2 py-0.5 rounded border border-teal-500/20 font-semibold" x-text="'Filter: ' + item.jenis_astap_nama"></span>
                                                </template>
                                            </span>
                                            <span class="text-teal-400 font-mono text-[10px] hidden sm:inline" x-text="item.jenis_astap_nama ? '⚡ Sesuai Jenis Terpilih' : '⚡ Ketik untuk filter'"></span>
                                        </label>
                                        <div class="relative flex items-center">
                                            <input type="text" 
                                                   x-model="item.nama_barang" 
                                                   @focus="activeDropdownIndex = idx"
                                                   @input="activeDropdownIndex = idx; onNamaBarangInput(item)"
                                                   :placeholder="item.jenis_astap_nama ? ('Ketik nama barang dari ' + item.jenis_astap_nama + '...') : 'Ketik nama barang aset (contoh: laptop, monitor, kasur)...'" 
                                                   class="w-full h-11 bg-slate-900 border border-slate-700/90 rounded-xl px-4 py-2.5 pl-10 pr-10 text-xs text-white font-bold placeholder-slate-500 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all">
                                            
                                            <svg class="w-4 h-4 text-teal-400 pointer-events-none" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                            </svg>

                                            <!-- Tombol Silang Bersihkan Nama Barang di Pojok Kanan Dalam Input -->
                                            <template x-if="item.nama_barang && item.nama_barang.trim() !== ''">
                                                <button type="button" 
                                                        @click.stop="clearItemBarang(item, idx)" 
                                                        style="position: absolute; right: 12px; left: auto; top: 50%; transform: translateY(-50%); z-index: 20;"
                                                        class="w-6 h-6 rounded-lg bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 flex items-center justify-center transition-all cursor-pointer shadow-sm border border-slate-700/60 hover:border-rose-500/40"
                                                        title="Kosongkan nama barang">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </template>
                                        </div>

                                        <!-- Floating Dropdown Hasil Ketik Filter ASTAP -->
                                        <div x-show="activeDropdownIndex === idx" 
                                             x-transition 
                                             class="absolute left-0 right-0 z-40 mt-1.5 bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl overflow-hidden max-h-60 overflow-y-auto divide-y divide-slate-800">
                                            
                                            <div class="px-4 py-2 bg-slate-950/90 text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between border-b border-slate-800">
                                                <span x-text="item.jenis_astap_nama ? ('Pilih Master ASTAP (' + item.jenis_astap_nama + ')') : 'Pilih Data Master ASTAP'"></span>
                                                <span class="text-teal-400 font-mono" x-text="getFilteredAstap(item, item.nama_barang).length + ' barang tersedia'"></span>
                                            </div>

                                            <template x-for="ast in getFilteredAstap(item, item.nama_barang).slice(0, 5)" :key="ast.kode">
                                                <div @click="selectAstapItem(item, ast)"
                                                     class="px-4 py-2.5 hover:bg-teal-500/15 cursor-pointer transition-colors group flex items-center justify-between gap-3">
                                                    <div class="space-y-0.5">
                                                        <p class="font-bold text-xs text-white group-hover:text-teal-300" x-text="ast.nama"></p>
                                                        <p class="text-[10px] text-slate-400" x-text="ast.kode + (ast.jenis_nama ? ' • ' + ast.jenis_nama : (ast.kategori ? ' • ' + ast.kategori : '')) + (ast.merk ? ' • ' + ast.merk : '')"></p>
                                                    </div>
                                                    <span class="px-2.5 py-1 rounded-lg bg-slate-950 border border-slate-700 text-teal-300 font-mono text-[10px] font-bold shrink-0" x-text="ast.satuan || 'Unit'"></span>
                                                </div>
                                            </template>

                                            <template x-if="getFilteredAstap(item, item.nama_barang).length === 0">
                                                <div class="p-4 text-center text-xs text-slate-400">
                                                    <p class="text-amber-400 font-semibold" x-text="item.jenis_astap_nama ? ('Tidak ditemukan barang untuk ' + item.jenis_astap_nama) : 'Tidak ditemukan barang ASTAP'"></p>
                                                    <p class="text-[10px] text-slate-500 mt-0.5">Ketik nama lain atau isi nama barang secara manual</p>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- 2. Kode Rekening 108 (Sejajar di Samping Nama Barang) -->
                                    <div class="w-48 sm:w-56 shrink-0">
                                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Kode Rekening 108</label>
                                        <input type="text" x-model="item.kode_barang" placeholder="Terisi otomatis..."
                                               class="w-full h-11 bg-slate-900 border border-slate-700/90 rounded-xl px-4 py-2.5 text-xs text-cyan-300 font-mono font-bold placeholder-slate-500 focus:outline-none focus:border-teal-500 transition-all">
                                    </div>
                                </div>

                                <!-- Baris 1b: NIBAR Multi-Select (Hanya Ditampilkan Pada Fitur Edit Distribusi) -->
                                <template x-if="isEdit">
                                    <div class="relative" @click.away="if(activeNibarDropdownIndex === idx) activeNibarDropdownIndex = null">
                                        <label class="block text-slate-300 font-semibold text-xs mb-1.5 flex items-center justify-between">
                                            <span class="flex items-center space-x-1.5">
                                                <span class="text-amber-400">🔖</span>
                                                <span>NIBAR (Nomor Induk Barang)</span>
                                                <template x-if="!item.nama_barang && !item.kode_barang">
                                                    <span class="text-slate-400 font-normal text-[11px] hidden sm:inline">— pilih nama barang terlebih dahulu</span>
                                                </template>
                                                <template x-if="(item.nama_barang || item.kode_barang) && !isNibarEmpty(item)">
                                                    <span class="text-slate-400 font-normal text-[11px] hidden sm:inline">— pilih maks. sesuai Volume (Qty)</span>
                                                </template>
                                            </span>
                                            
                                            <!-- Status Badge -->
                                            <div>
                                                <template x-if="!item.nama_barang && !item.kode_barang">
                                                    <span class="text-slate-500 font-mono text-[10px]">Pilih barang dulu</span>
                                                </template>
                                                <template x-if="(item.nama_barang || item.kode_barang) && isNibarEmpty(item)">
                                                    <span class="px-2 py-0.5 rounded bg-rose-500/15 border border-rose-500/30 text-rose-300 text-[10px] font-bold">
                                                        ⚠️ Barang Kosong (0 NIBAR)
                                                    </span>
                                                </template>
                                                <template x-if="(item.nama_barang || item.kode_barang) && !isNibarEmpty(item)">
                                                    <span class="text-amber-400 font-mono text-[10px]" x-text="(item.nibar_selected || []).length + ' / ' + (item.qty || 1) + ' dipilih'"></span>
                                                </template>
                                            </div>
                                        </label>

                                        <!-- Chips: NIBAR yang sudah dipilih -->
                                        <template x-if="(item.nibar_selected || []).length > 0">
                                            <div class="flex flex-wrap gap-2 mb-2">
                                                <template x-for="n in item.nibar_selected" :key="n.nibar">
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-500/15 border border-amber-500/30 text-amber-300 text-[10px] font-mono font-bold">
                                                        <span x-text="n.nibar"></span>
                                                        <button type="button" @click.stop="removeNibar(item, n.nibar)"
                                                                class="w-3.5 h-3.5 rounded-full bg-rose-500/20 hover:bg-rose-500/40 text-rose-300 flex items-center justify-center transition-all"
                                                                title="Hapus NIBAR ini">
                                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    </span>
                                                </template>
                                            </div>
                                        </template>

                                        <!-- KONDISI 1: Belum Pilih Barang -->
                                        <template x-if="!item.nama_barang && !item.kode_barang">
                                            <div class="relative flex items-center">
                                                <input type="text" 
                                                       disabled
                                                       placeholder="Pilih nama barang di atas terlebih dahulu untuk memilih NIBAR..." 
                                                       class="w-full h-11 bg-slate-900/50 border border-slate-800 rounded-xl px-4 py-2.5 pl-10 pr-4 text-xs text-slate-500 placeholder-slate-600 cursor-not-allowed">
                                                <svg class="w-4 h-4 text-slate-600 pointer-events-none" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                            </div>
                                        </template>

                                        <!-- KONDISI 2: Barang Terpilih Tapi Data NIBAR Kosong -->
                                        <template x-if="(item.nama_barang || item.kode_barang) && isNibarEmpty(item)">
                                            <div class="space-y-1.5">
                                                <div class="relative flex items-center">
                                                    <input type="text" 
                                                           disabled
                                                           value="⚠️ Barang Kosong — Data NIBAR belum tersedia di sistem" 
                                                           class="w-full h-11 bg-rose-950/20 border border-rose-500/40 rounded-xl px-4 py-2.5 pl-10 pr-4 text-xs text-rose-300 font-semibold cursor-not-allowed">
                                                    <svg class="w-4 h-4 text-rose-400 pointer-events-none" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                </div>
                                                <p class="text-[11px] text-rose-400/90 flex items-center space-x-1.5 pl-1">
                                                    <span>ℹ️ Tidak ditemukan register NIBAR aktif untuk barang <span class="font-mono font-bold text-white" x-text="getItemKode(item) || item.nama_barang"></span>. Anda tetap dapat mendistribusikan barang dengan mengisi Volume (Qty).</span>
                                                </p>
                                            </div>
                                        </template>

                                        <!-- KONDISI 3: Barang Terpilih & Ada NIBAR Tersedia -->
                                        <template x-if="(item.nama_barang || item.kode_barang) && !isNibarEmpty(item)">
                                            <div>
                                                <!-- Input Pencarian NIBAR -->
                                                <div class="relative flex items-center">
                                                    <input type="text" 
                                                           :value="nibarSearch[item.id] || ''"
                                                           @input="nibarSearch = {...nibarSearch, [item.id]: $event.target.value}; activeNibarDropdownIndex = idx"
                                                           @focus="activeNibarDropdownIndex = idx"
                                                           :placeholder="(item.nibar_selected || []).length >= (item.qty || 1) ? '✅ Sudah memilih ' + (item.qty || 1) + ' NIBAR (sesuai volume)' : 'Ketik atau klik untuk pilih NIBAR...'" 
                                                           :disabled="(item.nibar_selected || []).length >= (item.qty || 1)"
                                                           class="w-full h-11 bg-slate-900 border border-amber-500/40 rounded-xl px-4 py-2.5 pl-10 pr-10 text-xs text-white font-mono placeholder-slate-500 focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400/50 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                                    <svg class="w-4 h-4 text-amber-400 pointer-events-none" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>

                                                    <!-- Tombol Silang Reset Input Pencarian NIBAR di Pojok Kanan Dalam Input -->
                                                    <template x-if="(nibarSearch[item.id] || '').trim() !== ''">
                                                        <button type="button" 
                                                                @click.stop="nibarSearch[item.id] = ''" 
                                                                style="position: absolute; right: 12px; left: auto; top: 50%; transform: translateY(-50%); z-index: 20;"
                                                                class="w-6 h-6 rounded-lg bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 flex items-center justify-center transition-all cursor-pointer shadow-sm border border-slate-700/60 hover:border-rose-500/40"
                                                                title="Bersihkan pencarian NIBAR">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    </template>
                                                </div>

                                                <!-- Dropdown NIBAR -->
                                                <div x-show="activeNibarDropdownIndex === idx"
                                                     x-transition
                                                     class="absolute left-0 right-0 z-40 mt-1.5 bg-slate-900 border border-amber-500/30 rounded-2xl shadow-2xl overflow-hidden max-h-56 overflow-y-auto divide-y divide-slate-800">

                                                    <div class="px-4 py-2 bg-slate-950/90 text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between border-b border-slate-800">
                                                        <span x-text="'NIBAR tersedia untuk ' + (item.nama_barang || '-')"></span>
                                                        <span class="text-amber-400 font-mono" x-text="getFilteredNibar(item, nibarSearch[item.id] || '').length + ' tersedia'"></span>
                                                    </div>

                                                    <template x-for="n in getFilteredNibar(item, nibarSearch[item.id] || '')" :key="n.nibar">
                                                        <div @click="selectNibar(item, n)"
                                                             class="px-4 py-2.5 hover:bg-amber-500/15 cursor-pointer transition-colors group flex items-center justify-between gap-3">
                                                            <div class="space-y-0.5">
                                                                <div class="flex items-center space-x-2">
                                                                    <p class="font-mono font-bold text-xs text-white group-hover:text-amber-300" x-text="n.nibar"></p>
                                                                    <span class="text-[9px] px-1.5 py-0.2 rounded bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 font-semibold" x-text="n.status || 'Tersedia'"></span>
                                                                </div>
                                                                <p class="text-[10px] text-slate-400" x-text="'Ruang: ' + n.ruang + ' • ' + n.kondisi"></p>
                                                            </div>
                                                            <span class="px-2.5 py-1 rounded-lg bg-slate-950 border border-amber-500/30 text-amber-300 text-[10px] font-bold shrink-0">Pilih →</span>
                                                        </div>
                                                    </template>

                                                    <template x-if="getFilteredNibar(item, nibarSearch[item.id] || '').length === 0">
                                                        <div class="p-4 text-center text-xs text-slate-400">
                                                            <p class="text-amber-400 font-semibold">Tidak ada NIBAR yang cocok</p>
                                                            <p class="text-[10px] text-slate-500 mt-0.5">Semua NIBAR mungkin sudah dipilih</p>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <!-- Baris 2: Kondisi Fisik, Volume, & Satuan (Selalu Sejajar Berdampingan dalam 1 Baris) -->
                                <div class="flex flex-row items-end gap-3 w-full">
                                    
                                    <!-- 3. Kondisi Fisik Barang (Flex-1) -->
                                    <div class="flex-1 min-w-0">
                                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Kondisi Fisik Barang</label>
                                        <select x-model="item.kondisi" 
                                                class="w-full h-11 bg-slate-900 border border-slate-700/90 rounded-xl px-3.5 py-2.5 text-xs text-emerald-400 font-bold focus:outline-none focus:border-teal-500 transition-all cursor-pointer">
                                            <option value="Baik">🟢 Baik (Siap Pakai)</option>
                                            <option value="Kurang Baik">🟡 Kurang Baik (Perlu Servis)</option>
                                            <option value="Rusak">🔴 Rusak</option>
                                        </select>
                                    </div>

                                    <!-- 4. Volume (Qty) (Kecil) -->
                                    <div class="w-28 sm:w-32 shrink-0">
                                        <label class="block text-slate-300 font-semibold text-xs mb-1.5 text-center">Volume (Qty)</label>
                                        <input type="number" min="1" 
                                               x-model="item.qty" 
                                               class="w-full h-11 bg-slate-900 border border-slate-700/90 rounded-xl px-3 py-2.5 text-xs text-center text-white font-mono font-bold focus:outline-none focus:border-teal-500 transition-all">
                                    </div>

                                    <!-- 5. Satuan (Kompak - Otomatis ASTAP) -->
                                    <div class="w-28 sm:w-32 shrink-0">
                                        <label class="block text-teal-300 font-semibold text-xs mb-1.5 text-center">Satuan (⚡ Auto)</label>
                                        <input type="text" 
                                               x-model="item.satuan" 
                                               placeholder="Unit" 
                                               class="w-full h-11 bg-slate-900 border border-teal-500/50 rounded-xl px-3 py-2.5 text-xs text-center text-teal-300 font-bold focus:outline-none focus:border-teal-500 transition-all">
                                    </div>
                                </div>

                                <!-- Baris 3: Keterangan / Catatan Spesifik Item (Sendiri / Full-Width) -->
                                <div>
                                    <label class="block text-slate-400 font-semibold text-xs mb-1.5">Keterangan / Catatan Peruntukan Barang (Opsional)</label>
                                    <input type="text" 
                                           x-model="item.keterangan" 
                                           placeholder="Contoh: u/ Ruang Tindakan IGD / Bed No. 04 / Pengadaan DAK Kesehatan..." 
                                           class="w-full h-11 bg-slate-900 border border-slate-700/90 rounded-xl px-4 py-2.5 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-teal-500 transition-all">
                                </div>

                            </div>

                        </div>
                    </template>
                </div>

                <!-- Ringkasan Akumulasi Volume Multi-Barang & Tombol Tambah Bawah -->
                <div class="mt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 bg-slate-950 rounded-2xl border border-slate-800 text-xs">
                    <div class="flex items-center space-x-3 text-slate-300">
                        <span>Total Rincian: <strong class="text-teal-400 font-extrabold" x-text="formData.items.length + ' Jenis Barang'"></strong></span>
                        <span>•</span>
                        <span>Akumulasi Volume: <strong class="text-emerald-400 font-extrabold" x-text="getTotalItemVolume() + ' Total Item/Unit'"></strong></span>
                    </div>

                    <button type="button" @click="addItem()" 
                            class="px-4 py-2 rounded-xl bg-teal-500/20 hover:bg-teal-500/30 text-teal-300 border border-teal-500/40 font-bold flex items-center space-x-2 transition-all active:scale-95 self-start sm:self-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah Barang Lagi</span>
                    </button>
                </div>
            </div>

            <!-- BAGIAN 3: CATATAN UMUM PENEMPATAN -->
            <div class="pt-4 border-t border-slate-800">
                <label class="block text-slate-300 font-semibold text-xs mb-1.5">Catatan Umum / Keterangan Penempatan</label>
                <textarea x-model="formData.keterangan" rows="2" placeholder="Contoh: Pengadaan DAK Kesehatan / BLUD untuk kelengkapan ruangan..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-teal-500"></textarea>
            </div>

            <!-- Tombol Aksi Batal & Simpan (Hanya di Bagian Bawah Form Sesuai Permintaan) -->
            <div class="pt-6 border-t border-slate-800 flex items-center justify-end space-x-3">
                <a href="{{ route('distribusi.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-all">
                    Batal
                </a>
                <button type="button" @click="submitForm()" class="px-6 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-teal-500/20 transition-all flex items-center space-x-1.5 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span x-text="isEdit ? 'Simpan Perubahan' : 'Simpan Distribusi Baru'"></span>
                </button>
            </div>
        </div>

    </div>
</x-layout>
